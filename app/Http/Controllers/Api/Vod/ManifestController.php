<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Services\VodService;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __construct(
        protected VodService $vodService
    ) {
    }

    public function __invoke(string $token): JsonResponse
    {
        abort_if(!$this->vodService->validToken($token), 403);

        $tokenData = collect($this->vodService->decodeToken($token));

        $contents = $this->vodService->getSequencesFormat(
            $tokenData->get('video')
        );

        return response()->json($contents);
    }
}
