<?php

namespace App\Models;

use App\Support\Scout\MediaIndexConfigurator;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Relateable;
use App\Traits\Securable;
use App\Traits\Streamable;
use App\Traits\Taggable;
use App\Traits\Viewable as ViewableHelpers;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;
use CyrildeWit\EloquentViewable\Viewable;
use Illuminate\Support\Facades\URL;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\Models\Media as BaseMedia;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Media extends BaseMedia implements ViewableContract
{
    use Hashidable;
    use HasJsonRelationships;
    use HasStatuses;
    use HasTags;
    use Randomable;
    use Relateable;
    use Searchable;
    use Sluggable;
    use Securable;
    use SluggableScopeHelpers;
    use Streamable;
    use Taggable;
    use Viewable;
    use ViewableHelpers;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @var string
     */
    protected $indexConfigurator = MediaIndexConfigurator::class;

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
            ],
            'description' => [
                'type' => 'text',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * @return string
     */
    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * @return MorphToMany
     */
    public function tags()
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }

    /**
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        if (!$this->hasGeneratedConversion('thumbnail')) {
            return asset('storage/images/placeholders/empty.png');
        }

        return $this->getAssetUrlAttribute('thumbnail');
    }

    /**
     * @return string
     */
    public function getPreviewAttribute(): string
    {
        if (!$this->hasGeneratedConversion('preview')) {
            return asset('storage/images/placeholders/blank.mp4');
        }

        return $this->getAssetUrlAttribute('preview');
    }

    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)->unique()->count();
    }

    /**
     * @return string
     */
    public function getAssetUrlAttribute(string $conversion = null): string
    {
        return URL::signedRoute('api.asset.show', [
            'media' => $this,
            'user' => auth()->user(),
            'type' => $conversion,
            'version' => $this->updated_at->timestamp,
        ]);
    }

    /**
     * @return string
     */
    public function getStreamUrlAttribute(): string
    {
        return self::getSecureExpireLink(
            $this->getStreamJsonUrl(),
            config('vod.secret'),
            3600,
            $this->id,
            request()->ip()
        );
    }

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return $this->getTemporaryUrl(
            Carbon::now()->addHours(4)
        );
    }
}
