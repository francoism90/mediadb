<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $tags = [
        [
            'name' => 'Action',
            'type' => 'genre',
        ],
        [
            'name' => 'Adventure',
            'type' => 'genre',
        ],
        [
            'name' => 'Animation',
            'type' => 'genre',
        ],
        [
            'name' => 'Comedy',
            'type' => 'genre',
        ],
        [
            'name' => 'Crime',
            'type' => 'genre',
        ],
        [
            'name' => 'Drama',
            'type' => 'genre',
        ],
        [
            'name' => 'Fantasy',
            'type' => 'genre',
        ],
        [
            'name' => 'Historical',
            'type' => 'genre',
        ],
        [
            'name' => 'Horror',
            'type' => 'genre',
        ],
        [
            'name' => 'Mystery',
            'type' => 'genre',
        ],
        [
            'name' => 'Political',
            'type' => 'genre',
        ],
        [
            'name' => 'Romance',
            'type' => 'genre',
        ],
        [
            'name' => 'Science Fiction',
            'type' => 'genre',
        ],
        [
            'name' => 'Talk',
            'type' => 'genre',
        ],
        [
            'name' => 'Thriller',
            'type' => 'genre',
        ],
        [
            'name' => 'Dutch',
            'type' => 'language',
        ],
        [
            'name' => 'English',
            'type' => 'language',
        ],
        [
            'name' => 'John Doe',
            'type' => 'actor',
        ],
        [
            'name' => 'Jane Doe',
            'type' => 'actor',
        ],
        [
            'name' => 'Foo Bar',
            'type' => 'studio',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        foreach ($this->tags as $tag) {
            Tag::findOrCreateFromString($tag['name'], $tag['type']);
        }
    }
}
