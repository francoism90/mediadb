<?php

namespace App\Services\Tokenizers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LocalTokenizer implements TokenizerInterface
{
    /**
     * @param array $data
     * @param int   $expires
     *
     * @return string
     */
    public function create(array $data = [], int $expires = 60): string
    {
        $tokenKey = Str::uuid();

        Cache::tags(['tokens', 'local'])->put($tokenKey, $data, $expires);

        return $tokenKey;
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function exists(string $token): bool
    {
        return Cache::tags(['tokens', 'local'])->has($token);
    }

    /**
     * @param string $token
     *
     * @return array
     */
    public function find(string $token): array
    {
        return Cache::tags(['tokens', 'local'])->get($token);
    }
}
