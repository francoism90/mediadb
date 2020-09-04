<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;
use Throwable;

class CleanupService
{
    /**
     * @return void
     */
    public function cleanup(): void
    {
        $this->deleteExpiredStreamFiles();
    }

    /**
     * @return void
     */
    public function deleteExpiredStreamFiles(): void
    {
        $files = $this->getExpiredStreamFiles();

        foreach ($files as $file) {
            try {
                unlink($file->getRealPath());
            } catch (Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * @return Finder
     */
    protected function getExpiredStreamFiles(): Finder
    {
        $path = Storage::disk('streams')->path(null);

        return (new Finder())
            ->files()
            ->in($path)
            ->depth(0)
            ->date('until 3 days ago')
            ->name('*.json')
            ->sortByModifiedTime();
    }
}
