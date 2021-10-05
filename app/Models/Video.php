<?php

namespace App\Models;

use App\Traits\HasQueryCacheable;
use App\Traits\InteractsWithAcquaintances;
use App\Traits\InteractsWithDash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeFollowed;
use Multicaret\Acquaintances\Traits\CanBeViewed;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends BaseModel
{
    use CanBeFavorited;
    use CanBeFollowed;
    use CanBeViewed;
    use HasQueryCacheable;
    use HasTranslatableSlug;
    use InteractsWithAcquaintances;
    use InteractsWithDash;
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
        'duration',
        'resolution',
    ];

    /**
     * @var array
     */
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
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'actors' => $this->extractTagTranslations('name', 'actor'),
            'studios' => $this->extractTagTranslations('name', 'studio'),
            'genres' => $this->extractTagTranslations('name', 'genre'),
            'languages' => $this->extractTagTranslations('name', 'language'),
            'tags' => $this->extractTagTranslations('description'),
        ];
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('thumbnail')
            ->performOnCollections('clips');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('clips')
            ->acceptsMimeTypes(config('api.video.clips_mimetypes'))
            ->useDisk('media');

        $this
            ->addMediaCollection('captions')
            ->acceptsMimeTypes(config('api.video.captions_mimetypes'))
            ->useDisk('media');
    }

    public function getClipsAttribute(): MediaCollection
    {
        return $this->getMedia('clips')
            ?->sortByDesc([
                ['custom_properties->height', 'desc'],
                ['custom_properties->width', 'desc'],
            ]);
    }

    public function getClipAttribute(): ?Media
    {
        return $this->clips?->first();
    }

    public function getDurationAttribute(): ?float
    {
        return $this->clips?->max('custom_properties.duration');
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->clip?->getUrl('thumbnail');
    }

    public function getResolutionAttribute(): string
    {
        $collect = collect(config('api.video.resolutions'));

        $byHeight = $collect->firstWhere('height', '>=', $this->clip?->getCustomProperty('height', 0));
        $byWidth = $collect->firstWhere('width', '>=', $this->clip?->getCustomProperty('width', 0));

        return $byHeight['name'] ?? $byWidth['name'] ?? 'N/A';
    }

    public function getSpriteUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.videos.sprite',
            [
                'video' => $this,
                'version' => $this->updated_at->timestamp,
            ]
        );
    }

    public function getCaptureTimeAttribute(): ?string
    {
        return $this->extra_attributes->get('capture_time');
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

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with($this->with);
    }
}
