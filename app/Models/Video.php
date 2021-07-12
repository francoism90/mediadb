<?php

namespace App\Models;

use App\Traits\InteractsWithAcquaintances;
use App\Traits\InteractsWithScout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeFollowed;
use Multicaret\Acquaintances\Traits\CanBeViewed;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends BaseModel
{
    use CanBeFavorited;
    use CanBeFollowed;
    use CanBeViewed;
    use HasTranslatableSlug;
    use InteractsWithAcquaintances;
    use InteractsWithScout;
    use Searchable;

    /**
     * @var array
     */
    protected $with = [
        'media',
        'tags',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'clip',
    ];

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

    public function searchableAs(): string
    {
        return 'videos_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->extractTranslations('name'),
            'overview' => $this->extractTranslations('overview'),
            'season_number' => $this->extractLeadingZeroes($this->season_number),
            'episode_number' => $this->extractLeadingZeroes($this->episode_number),
            'tags' => $this->extractTagTranslations('name'),
            'tags_description' => $this->extractTagTranslations('description'),
        ];
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with($this->with);
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
            ->whereHas('favoriters', function (Builder $query): void {
                $query->where('subject_type', Video::class);
            })
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->where('interactions.relation', 'favorite')
            ->where('interactions.user_id', auth()?->user()?->id ?? 0)
            ->select('videos.*')
            ->latest('interactions.created_at');
    }

    public function scopeWithFollowing(Builder $query): Builder
    {
        return $query
            ->with('followers')
            ->whereHas('followers', function (Builder $query): void {
                $query->where('subject_type', Video::class);
            })
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->where('interactions.relation', 'follow')
            ->where('interactions.user_id', auth()?->user()?->id ?? 0)
            ->select('videos.*')
            ->latest('interactions.created_at');
    }

    public function scopeWithViewed(Builder $query): Builder
    {
        return $query
            ->with('viewers')
            ->whereHas('viewers', function (Builder $query): void {
                $query->where('subject_type', Video::class);
            })
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->where('interactions.relation', 'view')
            ->where('interactions.user_id', auth()?->user()?->id ?? 0)
            ->select('videos.*')
            ->latest('interactions.created_at');
    }
}
