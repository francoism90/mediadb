<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaStreamService;

class StreamController extends Controller
{
    /**
     * @var MediaStreamService
     */
    protected $mediaStreamService;

    public function __construct(MediaStreamService $mediaStreamService)
    {
        $this->mediaStreamService = $mediaStreamService;
    }

    /**
     * @param Media $media
     * @param User  $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Media $media, User $user)
    {
        $streamUrl = $this->mediaStreamService->getUrl($media, $user);

        return redirect($streamUrl);
    }
}
