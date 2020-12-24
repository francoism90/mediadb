<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Services\VideoStreamService;

class ManifestController extends Controller
{
    /**
     * @var VideoStreamService
     */
    protected $videoStreamService;

    public function __construct(VideoStreamService $videoStreamService)
    {
        $this->videoStreamService = $videoStreamService;
    }

    /**
     * @param string      $token
     * @param string|null $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(string $token, string $type = null)
    {
        if (!$this->videoStreamService->validToken($token)) {
            abort(403);
        }

        $tokenData = collect($this->videoStreamService->decodeToken($token));

        $collect = cache()->remember("{$token}_data", 300, function () use ($tokenData) {
            return collect([
                'video' => Video::findOrFail($tokenData->get('video')),
                'user' => User::findOrFail($tokenData->get('user')),
            ]);
        });

        // TODO: Validate user is able to stream (e.g. account subscriptions)

        $contents = $this->videoStreamService->getResponseFormat(
            $collect->get('video')
        );

        return response()->json($contents);
    }
}
