<?php

namespace App\Console\Commands\Video;

use App\Actions\Video\DeleteVideo;
use App\Models\Video;
use Illuminate\Console\Command;

class DeleteVideoCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:delete {video}';

    /**
     * @var string
     */
    protected $description = 'Delete a video model';

    public function handle(DeleteVideo $deleteVideo): void
    {
        $model = Video::findByHashidOrFail($this->argument('video'));

        $deleteVideo($model);
    }
}
