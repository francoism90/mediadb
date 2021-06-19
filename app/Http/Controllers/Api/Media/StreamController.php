<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\StreamService;
use Illuminate\Http\RedirectResponse;

class StreamController extends Controller
{
    public function __construct(
        protected StreamService $streamService
    ) {
    }

    public function __invoke(Media $media, User $user): RedirectResponse
    {
        $streamUrl = $this
            ->streamService
            ->getMappingUrl('dash', 'manifest.mpd', [
                'media' => $media,
                'user' => $user,
            ]);

        return redirect($streamUrl);
    }
}
