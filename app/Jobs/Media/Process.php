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

class Process implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        protected Media $media
    ) {
    }

    public function handle(
        UpdateMetadataDetails $updateMetadataDetails,
        CreateNewThumbnail $createNewThumbnail,
    ): void {
        $updateMetadataDetails->execute($this->media);
        $createNewThumbnail->execute($this->media);
    }

    public function tags(): array
    {
        return ['process', 'media:'.$this->media->id];
    }
}
