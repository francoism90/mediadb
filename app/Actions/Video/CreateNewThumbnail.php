<?php

namespace App\Actions\User;

use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;
use Symfony\Component\Finder\SplFileInfo;

class CreateNewVideo
{
    use QueueableAction;

    public function execute(User $user, SplFileInfo $file): Video
    {
        return $user->videos()->create([
            'name' => Str::title($file->getFilenameWithoutExtension()),
        ]);
    }
}
