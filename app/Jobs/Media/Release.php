<?php

namespace App\Jobs\Media;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Release implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public int $tries = 3;

    public int $timeout = 300;

    protected Media $media;

    public function __construct(Media $media)
    {
        $this->media = $media->withoutRelations();
    }

    public function handle(): void
    {
    }

    public function tags(): array
    {
        return ['release', 'media:'.$this->media->id];
    }
}
