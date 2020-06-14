<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;

class LikeController extends Controller
{
    /**
     * @param Media $media
     * @param User  $user
     *
     * @return mixed
     */
    public function __invoke(Media $media)
    {
    }
}
