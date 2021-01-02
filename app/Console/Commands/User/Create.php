<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Create extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:create {name} {email} {role=member}';

    /**
     * @var string
     */
    protected $description = 'Create an user model';

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
    public function handle(): void
    {
        $model = User::firstOrCreate(
            [
                'name' => $this->argument('name'),
                'email' => $this->argument('email'),
            ],
            [
                'password' => Hash::make(Str::random(16)),
            ]
        );

        $model->assignRole($this->argument('role'));

        // TODO: send welcome mail

        $this->info("Successfully created user {$model->name} ({$model->id})");
    }
}
