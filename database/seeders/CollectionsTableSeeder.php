<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CollectionsTableSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $collections = [
        [
            'name' => 'Movie Foo',
            'type' => 'title',
        ],
        [
            'name' => 'Movie Bar',
            'type' => 'title',
        ],
        [
            'name' => 'Serie 1',
            'type' => 'title',
        ],
        [
            'name' => 'Serie 2',
            'type' => 'title',
        ],
        [
            'name' => 'Favorite Series',
            'type' => null,
        ],
        [
            'name' => 'Favorite Movies',
            'type' => null,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::first();

        foreach ($this->collections as $collection) {
            $model = $user->collections()->create($collection);
            $model->setStatus('public');
        }
    }
}
