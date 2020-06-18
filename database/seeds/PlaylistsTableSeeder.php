<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class PlaylistsTableSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $playlists = [
        'Playlist 1',
        'Playlist 2',
        'Playlist 3',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::findBySlugOrFail('administrator');

        foreach ($this->playlists as $playlist) {
            $playlist = $user->playlists()->create([
                'name' => $playlist,
            ]);

            $playlist->setStatus('published');
        }
    }
}
