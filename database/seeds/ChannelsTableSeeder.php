<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChannelsTableSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $channels = [
        'Channel 1',
        'Channel 2',
        'Channel 3',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::findBySlugOrFail('administrator');

        foreach ($this->channels as $channel) {
            $channel = $user->channels()->create([
                'slug' => Str::slug($channel),
                'name' => $channel,
            ]);

            $channel->setStatus('published');
        }
    }
}
