<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagsTableSeeder extends Seeder
{
    /**
     * @var array
     */
    private $genres = [
        'Action',
        'Adventure',
        'Animation',
        'Comedy',
        'Crime',
        'Documentary',
        'Drama',
        'Fantasy',
        'Historical',
        'Horror',
        'Mystery',
        'Political',
        'Romance',
        'Science Fiction',
        'Thriller',
    ];

    /**
     * @var array
     */
    private $languages = [
        'English',
        'Dutch',
    ];

    /**
     * @var array
     */
    private $people = [
        'Homer Simpson',
        'Bart Simpson',
        'Marge Simpson',
        'Lisa Simpson',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Genres
        foreach ($this->genres as $genre) {
            // Generate slug
            $slug = Str::slug($genre, '-');

            DB::table('tags')->insert([
                'slug' => $this->getJsonByLocal($slug),
                'name' => $this->getJsonByLocal($genre),
                'type' => 'genre',
            ]);
        }

        // Languages
        foreach ($this->languages as $language) {
            // Generate slug
            $slug = Str::slug($language, '-');

            DB::table('tags')->insert([
                'slug' => $this->getJsonByLocal($slug),
                'name' => $this->getJsonByLocal($language),
                'type' => 'language',
            ]);
        }

        // People
        foreach ($this->people as $person) {
            // Generate slug
            $slug = Str::slug($person, '-');

            DB::table('tags')->insert([
                'slug' => $this->getJsonByLocal($slug),
                'name' => $this->getJsonByLocal($person),
                'type' => 'person',
            ]);
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getJsonByLocal(string $value, string $local = 'en'): string
    {
        return json_encode(
            [$local => $value],
            JSON_FORCE_OBJECT |
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );
    }
}
