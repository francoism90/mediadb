<?php

namespace App\Http\Controllers\Api\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\StoreRequest;
use App\Models\Collection;
use App\Models\Media;
use App\Traits\Hashidable;

class TrackController extends Controller
{
    /**
     * @param string $model
     * @param string $id
     *
     * @return void
     */
    public function __invoke(StoreRequest $request)
    {
        // Model bindings
        $models = [
            'collect' => Collection::class,
            'media' => Media::class,
        ];

        // Resolve model
        $resolve = Hashidable::getModelByKey(
            $request->input('id'),
            $models[$request->input('entity')]
        );

        $resolve->recordView('history', now()->addSeconds(30));
        $resolve->recordView('view_count', now()->addWeek());

        return response()->json('success');
    }
}
