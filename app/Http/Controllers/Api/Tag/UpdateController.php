<?php

namespace App\Http\Controllers\Api\Tag;

use App\Events\TagHasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class UpdateController extends Controller
{
    /**
     * @param UpdateRequest $request
     * @param Tag           $tag
     *
     * @return TagResource
     */
    public function __invoke(UpdateRequest $request, Tag $tag)
    {
        $locale = app()->getLocale();

        $tag->setTranslation('name', $locale, $request->input('name', $tag->name))
                   ->save();

        $tag->update([
            'type' => $request->input('type.id', $tag->type),
            'order_column' => $request->input('order_column', $tag->order_column),
        ]);

        event(new TagHasBeenUpdated($tag));

        return new TagResource($tag);
    }
}
