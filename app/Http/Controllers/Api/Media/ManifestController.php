<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Services\StreamService;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __construct(
        protected StreamService $streamService
    ) {
    }

    public function __invoke(string $token, string $type = null): JsonResponse
    {
        abort_if(!$this->streamService->validToken($token), 403);

        // TODO: Validate user is able to stream (e.g. account subscriptions)

        $tokenData = collect($this->streamService->decodeToken($token));

        $contents = $this->streamService->getResponseFormat(
            $tokenData->get('media')
        );

        return response()->json($contents);
    }
}
