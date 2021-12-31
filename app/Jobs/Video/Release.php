<?php

namespace App\Jobs\Video;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class Release implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public bool $failOnTimeout = true;

    public int $tries = 25;

    public function __construct(
        protected Video $video
    ) {
    }

    public function handle(): void
    {
    }

    public function middleware()
    {
        return [
            new WithoutOverlapping($this->video->id),
            new RateLimited(),
        ];
    }

    public function retryUntil(): \DateTime
    {
        return now()->addHour();
    }

    public function tags(): array
    {
        return ['release', 'video:'.$this->video->id];
    }
}
