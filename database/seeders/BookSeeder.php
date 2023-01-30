<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Book::insert([
            [
                'title' => 'Books 1',
                'author' => 'Author 1',
                'publisher' => 'Publisher 1',
                'release_year' => date('Y'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Books 2',
                'author' => 'Author 2',
                'publisher' => 'Publisher 2',
                'release_year' => date('Y'),
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
