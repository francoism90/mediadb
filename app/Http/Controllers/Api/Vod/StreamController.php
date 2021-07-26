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
        logger('hallo');

        $streamUrl = $this
            ->vodService
            ->generateUrl('dash', 'manifest.mpd', [
                'video' => $video,
            ]);

        logger($streamUrl);

        return redirect($streamUrl);
    }
}
