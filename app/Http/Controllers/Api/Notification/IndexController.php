<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Support\QueryBuilder\Sorts\FieldSorter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke()
    {
        $defaultSort = AllowedSort::custom('created_at', new FieldSorter())->defaultDirection('desc');

        $userNotifications = auth()->user()->notifications();

        $notifications = QueryBuilder::for($userNotifications)
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('updated_at', new FieldSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return NotificationResource::collection($notifications);
    }
}
