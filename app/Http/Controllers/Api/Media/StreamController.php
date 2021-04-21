<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaStreamService;
use Illuminate\Http\RedirectResponse;

class StreamController extends Controller
{
    public function __construct(
        protected MediaStreamService $mediaStreamService
    ) {
    }

    /**
     * @param Media $media
     * @param User  $user
     *
     * @return RedirectResponse
     */
    public function __invoke(Media $media, User $user): RedirectResponse
    {
        $streamUrl = $this->mediaStreamService->getMappingUrl($media, $user);

        return redirect($streamUrl);
    }
}
