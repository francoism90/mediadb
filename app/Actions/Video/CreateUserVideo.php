<?php

namespace App\Actions\Video;

use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class CreateUserVideo
{
    public function execute(User $user, SplFileInfo $file): Video
    {
        return $user->videos()->create([
            'name' => Str::title($file->getFilenameWithoutExtension()),
        ]);
    }
}
