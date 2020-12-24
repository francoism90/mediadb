<?php

namespace App\Services\Streamers;

interface StreamerInterface
{
    public function getUrl(): string;

    public function setToken(string $path): void;

    public function getToken(): string;
}
