<?php

use App\Broadcasting\MediaChannel;
use App\Broadcasting\TagChannel;
use App\Broadcasting\UserChannel;
use App\Broadcasting\VideoChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('media.{media}', MediaChannel::class);
Broadcast::channel('tag.{tag}', TagChannel::class);
Broadcast::channel('user.{user}', UserChannel::class);
Broadcast::channel('video.{video}', VideoChannel::class);
