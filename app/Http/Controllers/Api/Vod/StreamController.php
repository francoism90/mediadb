<?php

namespace App\Http\Controllers\Api\Vod;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VodService;
use Illuminate\Http\RedirectResponse;

class StreamController extends Controller
{
    public function __construct(
        protected VodService $vodService
    ) {
    }

    public function __invoke(Video $video): RedirectResponse
    {
        $streamUrl = $this
            ->vodService
            ->getTemporaryUrl(
                'dash',
                'manifest.mpd',
                [
                    'video' => $video,
                ]
            );

        return redirect($streamUrl);
    }
}
