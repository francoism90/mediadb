<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Services\MediaStreamService;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __construct(
        protected MediaStreamService $mediaStreamService
    ) {
    }

    /**
     * @param string      $token
     * @param string|null $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(string $token, string $type = null): JsonResponse
    {
        if (!$this->mediaStreamService->validToken($token)) {
            abort(403);
        }

        $tokenData = collect($this->mediaStreamService->decodeToken($token));

        // TODO: Validate user is able to stream (e.g. account subscriptions)

        $contents = $this->mediaStreamService->getResponseFormat(
            $tokenData->get('media')
        );

        return response()->json($contents);
    }
}
