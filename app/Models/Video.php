<?php

namespace App\Models;

use App\Traits\InteractsWithAcquaintances;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends BaseModel
{
    use CanBeFavorited;
    use HasTranslatableSlug;
    use InteractsWithAcquaintances;
    use Searchable;

    /**
     * @var array
     */
    public array $translatable = ['name', 'slug', 'overview'];

    /**
     * @var array
     */
    protected $with = ['media', 'tags'];

    /**
     * @var array
     */
    protected $appends = ['clip'];

    /**
     * @return morphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => array_values($this->getTranslations('name')),
            'overview' => array_values($this->getTranslations('overview')),
            'type' => $this->type,
            'status' => $this->status,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'tags' => $this->extractTagTranslations(),
        ];
    }

    /**
     * We need to register media conversion to use them.
     * Services do the actual conversion.
     *
     * @param Media $media
     */
    public function registerMediaConversions($media = null): void
    {
        $serviceConversions = [
            'thumbnail',
        ];

        foreach ($serviceConversions as $conversion) {
            $this->addMediaConversion($conversion)
                 ->withoutManipulations()
                 ->performOnCollections('service-collection')
                 ->nonQueued();
        }
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('clip')
            ->acceptsMimeTypes(
                config('video.clip_mimetypes')
            )
            ->singleFile()
            ->useDisk('media');

        $this
            ->addMediaCollection('caption')
            ->acceptsMimeTypes([
               config('video.caption_mimetypes'),
            ])
            ->useDisk('media');
    }

    /**
     * @return Media|null
     */
    public function getClipAttribute(): ?Media
    {
        return $this->getFirstMedia('clip')?->append([
            'duration',
            'resolution',
            'stream_url',
            'sprite_url',
            'thumbnail_url',
        ]);
    }

    /**
     * @return MediaCollection
     */
    public function getTracksAttribute(): MediaCollection
    {
        return $this->getMedia('caption');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithFavorites(Builder $query): Builder
    {
        return $query
            ->with('favoriters')
            ->whereHas('favoriters', function (Builder $query) {
                $query->where('user_id', auth()?->user()?->id || 0);
            })
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->select('videos.*')
            ->latest('interactions.created_at');
    }

    public function scopeWithDurations(Builder $query): Builder
    {

        return $query;
    }
}
