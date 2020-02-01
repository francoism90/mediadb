<?php

namespace App\Models;

use App\Support\Scout\CollectionIndexConfigurator;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Relateable;
use App\Traits\Taggable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;
use CyrildeWit\EloquentViewable\Viewable;
use Illuminate\Database\Eloquent\Model;
use ScoutElastic\Searchable;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Collection extends Model implements ViewableContract
{
    use Hashidable;
    use HasJsonRelationships;
    use HasStatuses;
    use HasTags;
    use Randomable;
    use Relateable;
    use Searchable;
    use Sluggable;
    use SluggableScopeHelpers;
    use Taggable;
    use Viewable;
    use ViewableHelpers;

    /**
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'json',
    ];

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
    protected $indexConfigurator = CollectionIndexConfigurator::class;

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
     * @return belongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return BelongsToJson
     */
    public function media()
    {
        return $this->BelongsToJson('App\Models\Media', 'custom_properties->media_ids');
    }

    /**
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        $model = $this->media()
            ->inRandomOrder(self::getRandomSeed(Media::class))
            ->first();

        return $model->thumbnail ?? asset('storage/images/placeholders/empty.png');
    }

    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)->unique()->count();
    }
}
