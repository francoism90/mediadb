<?php

namespace App\Jobs\Media;

use App\Actions\Media\CreateNewThumbnail;
use App\Actions\Media\UpdateMediaProperties;
use App\Models\Media;
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

    public function __construct(
        protected Media $media
    ) {
    }

    public function handle(
        UpdateMediaProperties $updateMediaProperties,
        CreateNewThumbnail $createNewThumbnail,
    ): void {
        $updateMediaProperties($this->media);
        $createNewThumbnail($this->media);
    }

    public function middleware()
    {
        return [
            new WithoutOverlapping($this->media->id),
            new RateLimited(),
        ];
    }

    public function retryUntil(): \DateTime
    {
        return now()->addHours(4);
    }

    public function tags(): array
    {
        return ['process', 'media:'.$this->media->id];
    }
}
