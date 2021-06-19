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

    public function __invoke(string $token, string $type = null): JsonResponse
    {
        abort_if(!$this->mediaStreamService->validToken($token), 403);

        // TODO: Validate user is able to stream (e.g. account subscriptions)

        $tokenData = collect($this->mediaStreamService->decodeToken($token));

        $contents = $this->mediaStreamService->getResponseFormat(
            $tokenData->get('media')
        );

        return response()->json($contents);
    }
}
