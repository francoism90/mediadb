<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\Media\StreamService;

class StreamController extends Controller
{
    /**
     * @var StreamService
     */
    protected $streamService;

    public function __construct(StreamService $streamService)
    {
        $this->streamService = $streamService;
    }

    /**
     * @param Media $media
     * @param User  $user
     *
     * @return RedirectResponse
     */
    public function __invoke(Media $media, User $user)
    {
        $streamUrl = $this->streamService->getExpireUrl($media);

        return redirect($streamUrl);
    }
}
