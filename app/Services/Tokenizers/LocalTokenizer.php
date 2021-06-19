<?php

namespace App\Services\Tokenizers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LocalTokenizer implements TokenizerInterface
{
    public function create(array $data = []): string
    {
        $key = Str::uuid();
        $expires = config('media.vod_expires', 60 * 24);

        Cache::tags(['tokens', 'local'])->put($key, $data, $expires);

        return $key;
    }

    public function exists(string $token): bool
    {
        return Cache::tags(['tokens', 'local'])->has($token);
    }

    public function find(string $token): array
    {
        return Cache::tags(['tokens', 'local'])->get($token);
    }
}
