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
    protected $with = ['media', 'statuses', 'tags'];

    /**
     * @var array
     */
    protected $appends = ['clip'];

    public array $translatable = [
        'name',
        'slug',
        'overview',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->extractTranslations('name'),
            'overview' => $this->extractTranslations('overview'),
            'type' => $this->type,
            'status' => $this->status,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'tags' => $this->extractTagTranslations(),
        ];
    }

    public function registerMediaConversions($media = null): void
    {
        $conversions = config('video.conversions', []);

        foreach ($conversions as $conversion) {
            $this->addMediaConversion($conversion)
                 ->withoutManipulations()
                 ->performOnCollections('conversion-service')
                 ->nonQueued();
        }
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('clip')
            ->acceptsMimeTypes(config('video.clip_mimetypes'))
            ->singleFile()
            ->useDisk('media');

        $this
            ->addMediaCollection('caption')
            ->acceptsMimeTypes(config('video.caption_mimetypes'))
            ->useDisk('media');
    }

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

    public function getTracksAttribute(): MediaCollection
    {
        return $this->getMedia('caption');
    }

    public function scopeWithFavorites(Builder $query): Builder
    {
        return $query
            ->with('favoriters')
            ->whereHas('favoriters', function (Builder $query) {
                $query->where('user_id', auth()->user()?->id);
            })
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->select('videos.*')
            ->latest('interactions.created_at');
    }
}
