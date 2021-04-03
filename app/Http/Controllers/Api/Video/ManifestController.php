<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Services\VideoStreamService;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __construct(protected VideoStreamService $videoStreamService)
    {
    }

    /**
     * @param string      $token
     * @param string|null $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(string $token, string $type = null): JsonResponse
    {
        if (!$this->videoStreamService->validToken($token)) {
            abort(403);
        }

        $tokenData = collect($this->videoStreamService->decodeToken($token));

        // TODO: Validate user is able to stream (e.g. account subscriptions)

        $contents = $this->videoStreamService->getResponseFormat(
            $tokenData->get('video')
        );

        return response()->json($contents);
    }
}
