<?php

namespace App\Models;

use App\Services\VodService;
use App\Traits\InteractsWithAcquaintances;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
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

    public function registerMediaConversions($media = null): void
    {
        $conversions = config('api.conversions');

        foreach ($conversions as $key => $value) {
            $this->addMediaConversion($key)
                 ->withoutManipulations()
                 ->performOnCollections('conversion-service')
                 ->nonQueued();
        }
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('clip')
            ->acceptsMimeTypes(config('api.sync.video_mimetypes'))
            ->singleFile()
            ->useDisk('media');

        $this
            ->addMediaCollection('caption')
            ->acceptsMimeTypes(config('api.sync.caption_mimetypes'))
            ->useDisk('media');
    }

    public function getClipAttribute(): ?Media
    {
        return $this->getFirstMedia('clip')?->append([
            'duration',
            'resolution',
        ]);
    }

    public function getPosterUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $this->getFirstMedia('clip'),
                'name' => 'thumbnail',
                'version' => $this->updated_at->timestamp,
            ]
        );
    }

    public function getSpriteUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.vod.sprite',
            [
                'video' => $this,
                'version' => $this->updated_at->timestamp,
            ]
        );
    }

    public function getVodUrlAttribute(): string
    {
        return app(VodService::class, ['model' => $this])
            ->getManifestUrl();
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
