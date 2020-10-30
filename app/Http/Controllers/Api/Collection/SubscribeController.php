<?php

namespace App\Http\Controllers\Api\Collection;

use App\Events\CollectionHasBeenSubscribed;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    /**
     * @param Request    $request
     * @param Collection $collection
     *
     * @return CollectionResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Collection $collection)
    {
        $user = auth()->user();

        if ($request->isMethod('delete')) {
            $user->unsubscribe($collection);
        } else {
            $user->subscribe($collection);
        }

        event(new CollectionHasBeenSubscribed($collection));

        return new CollectionResource($collection);
    }
}
