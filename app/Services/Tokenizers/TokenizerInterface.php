<?php

namespace App\Services\Tokenizers;

interface TokenizerInterface
{
    public function create(array $data = []): string;

    public function exists(string $token): bool;

    public function find(string $token): array;
}
