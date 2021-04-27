<?php

namespace App\Services\Streamers;

interface StreamerInterface
{
    public function getUrl(string $location, string $uri): string;

    public function setToken(string $path): void;

    public function getToken(): string;
}
