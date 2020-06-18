<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagsTableSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $genres = [
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
    protected $languages = [
        'English',
        'Dutch',
    ];

    /**
     * @var array
     */
    protected $people = [
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
            $slug = Str::slug($genre);

            DB::table('tags')->insert([
                'slug' => $this->getJsonByLocale($slug),
                'name' => $this->getJsonByLocale($genre),
                'type' => 'genre',
            ]);
        }

        // Languages
        foreach ($this->languages as $language) {
            $slug = Str::slug($language);

            DB::table('tags')->insert([
                'slug' => $this->getJsonByLocale($slug),
                'name' => $this->getJsonByLocale($language),
                'type' => 'language',
            ]);
        }

        // People
        foreach ($this->people as $person) {
            $slug = Str::slug($person);

            DB::table('tags')->insert([
                'slug' => $this->getJsonByLocale($slug),
                'name' => $this->getJsonByLocale($person),
                'type' => 'person',
            ]);
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function getJsonByLocale(string $value, string $locale = 'en'): string
    {
        return json_encode(
            [$locale => $value],
            JSON_FORCE_OBJECT |
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );
    }
}
