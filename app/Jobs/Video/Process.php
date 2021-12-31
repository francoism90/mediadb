<?php

namespace App\Jobs\Video;

use App\Actions\Video\CreateNewThumbnail;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class Process implements ShouldQueue
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

    public function handle(
        CreateNewThumbnail $createNewThumbnail,
    ): void {
        $createNewThumbnail($this->video);
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
        return ['process', 'video:'.$this->video->id];
    }
}
