<?php

namespace App\Jobs\Media;

use App\Actions\Media\CreateNewThumbnail;
use App\Actions\Media\UpdateMetadataDetails;
use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class Process implements ShouldQueue
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

    public function handle(
        UpdateMetadataDetails $updateMetadataDetails,
        CreateNewThumbnail $createNewThumbnail,
    ): void {
        $updateMetadataDetails($this->media);
        $createNewThumbnail($this->media);
    }

    public function middleware()
    {
        return [new RateLimited()];
    }

    public function retryUntil(): \DateTime
    {
        return now()->addDay();
    }

    public function tags(): array
    {
        return ['process', 'media:'.$this->media->id];
    }
}
