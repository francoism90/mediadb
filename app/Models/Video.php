<?php

namespace App\Models;

use App\Traits\InteractsWithAcquaintances;
use App\Traits\InteractsWithDash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
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
            'title' => $this->title,
            'production_code' => $this->production_code,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'duration' => $this->duration,
            'quality' => $this->quality,
            'views' => $this->views,
            'overview' => $this->extractTranslations('overview'),
            'actors' => $this->extractTagTranslations(type: 'actor'),
            'studios' => $this->extractTagTranslations(type: 'studio'),
            'genres' => $this->extractTagTranslations(type: 'genre'),
            'languages' => $this->extractTagTranslations(type: 'language'),
            'tags' => $this->extractTagTranslations(),
            'descriptions' => $this->extractTagTranslations('description'),
            'released' => $this->released_at,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
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

        $this
            ->addMediaCollection('thumbnail')
            ->acceptsMimeTypes(config('api.video.thumbnail_mimetypes'))
            ->useDisk('media')
            ->singleFile();
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
        return $this->getFirstMediaUrl('thumbnail');
    }

    public function getProductionCodeAttribute(): string
    {
        return implode('', array_filter([$this->season_number, $this->episode_number]));
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

    public function getThumbnailAttribute(): ?string
    {
        return $this->extra_attributes->get('thumbnail');
    }

    public function getTitleAttribute(): string
    {
        return implode(' - ', array_filter([$this->production_code, $this->name]));
    }

    public function getQualityAttribute(): string
    {
        $collect = collect(config('api.video.resolutions'));

        $byHeight = $collect->firstWhere('height', '>=', $this->clip?->getCustomProperty('height', 0));
        $byWidth = $collect->firstWhere('width', '>=', $this->clip?->getCustomProperty('width', 0));

        return $byHeight['name'] ?? $byWidth['name'] ?? 'N/A';
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with($this->with);
    }

    public function scopeActive(Builder $query): Builder
    {
        // TODO: check 'published' state
        return $query;
    }

    public function scopeIsTrending(Builder $query, int $days = 3): Builder
    {
        return $query
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->withCount(['viewers' => fn (Builder $builder) => $builder
                ->where('interactions.relation', 'view')
                ->where('interactions.created_at', '>=', Carbon::now()->subDays($days)),
            ])
            ->orderByDesc('viewers_count');
    }

    public function scopeUserFavorites(Builder $query, ?User $user = null): Builder
    {
        return $query
            ->with('favoriters')
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->where('interactions.relation', 'favorite')
            ->where('interactions.user_id', $user?->id ?? 0)
            ->select('videos.*')
            ->latest('interactions.created_at');
    }

    public function scopeUserFollowing(Builder $query, ?User $user = null): Builder
    {
        return $query
            ->with('followers')
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->where('interactions.relation', 'follow')
            ->where('interactions.user_id', $user?->id ?? 0)
            ->select('videos.*')
            ->latest('interactions.created_at');
    }

    public function scopeUserViewed(Builder $query, ?User $user = null): Builder
    {
        return $query
            ->with('viewers')
            ->join('interactions', 'videos.id', '=', 'interactions.subject_id')
            ->where('interactions.relation', 'view')
            ->where('interactions.user_id', $user?->id ?? 0)
            ->select('videos.*')
            ->latest('interactions.created_at');
    }

    public function scopeWithAnyType(Builder $query, ?array $types = [], ?User $user = null): Builder
    {
        $type = fn (string $key) => in_array($key, $types);

        return Video::active()
            ->when($type('favorites'), fn ($query) => $query->userFavorites($user))
            ->when($type('following'), fn ($query) => $query->userFollowing($user))
            ->when($type('random'), fn ($query) => $query->inRandomOrder())
            ->when($type('viewed'), fn ($query) => $query->userViewed($user));
    }
}
