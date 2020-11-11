<?php

namespace App\Services;

use App\Events\MediaHasBeenAdded;
use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Media;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class LibraryService
{
    /**
     * @var MediaService
     */
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @param Model       $model
     * @param SplFileInfo $file
     * @param string      $collection
     * @param array       $properties
     *
     * @return void
     */
    public function import(
        Model $model,
        SplFileInfo $file,
        string $collection = null,
        array $properties = []
    ): void {
        try {
            $filePath = $file->getRealPath();
            $fileExtension = $file->getExtension();

            $media = $model
                    ->addMedia($filePath)
                    ->withCustomProperties($properties)
                    ->toMediaCollection($collection);

            // Force WebVTT
            if ('vtt' === $fileExtension) {
                $media->mime_type = 'text/vtt';
                $media->save();
            }

            event(new MediaHasBeenAdded($model, $media));
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * @return void
     */
    public function performMaintenance(): void
    {
        $this->deleteExpiredStreamFiles();
        $this->setMissingMetadata();
        $this->hasMissingConversions();
    }

    /**
     * @param string $path
     *
     * @return Finder
     */
    public function getFilesInPath(string $path): Finder
    {
        $filter = function (SplFileInfo $file) {
            if (!$file->isReadable() || !$file->isWritable()) {
                return false;
            }

            $mime = mime_content_type($file->getRealPath());

            return in_array($mime, $this->supportedMimeTypes());
        };

        return (new Finder())
            ->files()
            ->in($path)
            ->depth('== 0')
            ->name($this->supportedFileNames())
            ->size($this->supportedFileSize())
            ->filter($filter)
            ->sortByName();
    }

    /**
     * @return void
     */
    protected function setMissingMetadata(): void
    {
        $models = $this->getMissingMetadataModels();

        foreach ($models as $model) {
            $this->mediaService->setAttributes($model);
        }
    }

    /**
     * @return void
     */
    protected function hasMissingConversions(): void
    {
        $models = $this->getMissingConversionModels();

        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail')) {
                CreateThumbnail::dispatch($model)->onQueue('optimize');
            }

            if (!$model->hasGeneratedConversion('sprite')) {
                CreateSprite::dispatch($model)->onQueue('optimize');
            }
        }
    }

    /**
     * @return void
     */
    protected function deleteExpiredStreamFiles(): void
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
     * @return LazyCollection
     */
    protected function getMissingMetadataModels(): LazyCollection
    {
        return Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->metadata')
            ->orWhereNull('custom_properties->metadata->duration')
            ->orWhereNull('custom_properties->metadata->width')
            ->orWhereNull('custom_properties->metadata->height')
            ->cursor();
    }

    /**
     * @return LazyCollection
     */
    protected function getMissingConversionModels(): LazyCollection
    {
        return Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->generated_conversions')
            ->orWhereNull('custom_properties->generated_conversions->sprite')
            ->orWhereNull('custom_properties->generated_conversions->thumbnail')
            ->cursor();
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

    /**
     * @return array
     */
    protected function supportedFileNames(): array
    {
        $extensions = collect(
            config('library.extensions')
        );

        return $extensions->map(function ($item) {
            return "*.{$item}";
        })->toArray();
    }

    /**
     * @return array
     */
    protected function supportedFileSize(): array
    {
        return config('library.sizes');
    }

    /**
     * @return array
     */
    protected function supportedLocales(): array
    {
        return config('library.locales');
    }

    /**
     * @return array
     */
    protected function supportedMimeTypes(): array
    {
        return config('library.mimetypes');
    }
}
