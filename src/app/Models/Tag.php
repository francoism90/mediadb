<?php

namespace App\Models;

use App\Traits\Hashidable;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;
use CyrildeWit\EloquentViewable\Viewable;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel implements ViewableContract
{
    use Hashidable;
    use Viewable;

    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)->unique()->count();
    }
}
