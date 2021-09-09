<?php

namespace App\Models;

use App\Traits\InteractsWithAcquaintances;
use App\Traits\InteractsWithVod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeFollowed;
use Multicaret\Acquaintances\Traits\CanBeViewed;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends BaseModel
{
    use CanBeFavorited;
    use CanBeFollowed;
    use CanBeViewed;
    use HasTranslatableSlug;
    use InteractsWithAcquaintances;
    use InteractsWithVod;
    use Searchable;

    /**
     * @var array
     */
    protected $with = [
        'media',
        'tags',
    ];

    public array $translatable = [
        'name',
        'slug',
        'overview',
    ];

    public bool $registerMediaConversionsUsingModelInstance = true;

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
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'actors' => $this->extractTagTranslations('name', 'actor'),
            'studios' => $this->extractTagTranslations('name', 'studio'),
            'genres' => $this->extractTagTranslations('name', 'genre'),
            'languages' => $this->extractTagTranslations('name', 'language'),
            'tags' => $this->extractTagTranslations('description'),
        ];
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with($this->with);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('thumbnail')
            ->extractVideoFrameAtSecond($this->thumbnail ?? 1)
            ->width(160)
            ->height(90)
            ->sharpen(10)
            ->performOnCollections('clips');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('clips')
            ->acceptsMimeTypes(config('api.sync.video_mimetypes'))
            ->singleFile()
            ->useDisk('media');

        $this
            ->addMediaCollection('captions')
            ->acceptsMimeTypes(config('api.sync.caption_mimetypes'))
            ->useDisk('media');
    }

    public function getClipAttribute(): ?Media
    {
        return $this->getFirstMedia('clips')?->append([
            'metadata',
        ]);
    }

    public function getPosterUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $this->getFirstMedia('clips'),
                'name' => 'thumbnail',
                'version' => $this->updated_at->timestamp,
            ]
        );
    }

    public function getThumbnailAttribute(): ?float
    {
        return $this->clip?->getCustomProperty('thumbnail');
    }

    public function scopeWithFavorites(Builder $query): Builder
    {
        return $query
            ->with('favoriters')
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
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->where('interactions.relation', 'view')
            ->where('interactions.user_id', auth()?->user()?->id ?? 0)
            ->select('videos.*')
            ->latest('interactions.created_at');
    }
}
