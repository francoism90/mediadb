<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaStreamService;
use Illuminate\Support\Str;

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
     * @return RedirectResponse
     */
    public function __invoke(Media $media, User $user)
    {
        $streamKey = (string) Str::uuid();

        $streamUrl = $this->mediaStreamService
            ->getExpireUrl(
                $media,
                $streamKey,
                request()->ip()
            );

        return redirect($streamUrl);
    }
}
