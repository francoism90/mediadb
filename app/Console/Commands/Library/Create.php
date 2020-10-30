<?php

namespace App\Console\Commands\Library;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class Create extends Command
{
    /**
     * @var string
     */
    protected $signature = 'library:create {name} {type=video} {user=1} {status=public}';

    /**
     * @var string
     */
    protected $description = 'Create a library model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $model = $this->createModel();

        $this->info(
            "Successfully created {$this->argument('type')} model ({$model->id})"
        );
    }

    /**
     * @return Model
     */
    protected function createModel(): Model
    {
        $user = $this->getUserModel();

        switch ($this->argument('type')) {
            default:
                $model = $user->videos()->create([
                    'name' => $this->argument('name'),
                ]);

                $model->setStatus($this->argument('status'));
        }

        return $model;
    }

    /**
     * @return User
     */
    protected function getUserModel(): User
    {
        return User::findOrFail(
            $this->argument('user')
        );
    }
}
