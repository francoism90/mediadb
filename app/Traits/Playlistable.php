<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Support\Collection;

trait Playlistable
{
    /**
     * @param array  $items
     * @param string $status
     *
     * @return Collection
     */
    public function firstOrCreatePlaylists(array $items = [], string $status = 'published'): Collection
    {
        $collection = collect();

        foreach ($items as $item) {
            $model = $this->playlists()->firstOrCreate(
                ['name' => $item['name']]
            );

            if (!$model->status()) {
                $model->setStatus($status);
            }

            $collection->push($model);
        }

        return $collection;
    }

    /**
     * @param Media $model
     * @param array $items
     *
     * @return void
     */
    public function syncPlaylistsWithMedia(Media $media, array $items = [], array $attributes = [])
    {
        // Make sure the playlists exists
        $playlists = $this->firstOrCreatePlaylists($items);

        // Get the current playlists
        $currentPlaylists = $this->playlists()->with('media')->get();

        // Sync to all playlists
        foreach ($currentPlaylists as $playlist) {
            $hasItem = $playlist->media->firstWhere('id', $media->id);
            $wantsAttach = $playlists->firstWhere('id', $playlist->id);

            if ($wantsAttach && !$hasItem) {
                $playlist->media()->attach([$media->id => $attributes])->save();
            } elseif (!$wantsAttach && $hasItem) {
                $playlist->media()->detach([$media->id])->save();
            }
        }
    }
}
