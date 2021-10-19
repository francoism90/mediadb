<?php

namespace App\Jobs\Media;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class Release implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        protected Media $media
    ) {
    }

    public function handle(): void
    {
    }

    public function middleware()
    {
        return [new RateLimited()];
    }

    public function retryUntil(): \DateTime
    {
        return now()->addHours(4);
    }

    public function tags(): array
    {
        return ['release', 'media:'.$this->media->id];
    }
}
