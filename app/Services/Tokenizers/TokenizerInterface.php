<?php

namespace App\Services\Tokenizers;

interface TokenizerInterface
{
    public function create(array $data = [], int $expires = 60): string;

    public function exists(string $token): bool;

    public function find(string $token): array;
}
