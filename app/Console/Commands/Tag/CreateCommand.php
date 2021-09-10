<?php

namespace App\Console\Commands\Tag;

use App\Actions\Tag\CreateNewTag;
use Illuminate\Console\Command;

class CreateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tag:create {name} {type=genre} {locale?}';

    /**
     * @var string
     */
    protected $description = 'Create a tag model';

    public function handle(): void
    {
        app(CreateNewTag::class)->execute($this->arguments());
    }
}
