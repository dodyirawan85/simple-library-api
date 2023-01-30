<?php

namespace Database\Seeders;

use App\Models\BookCategory;
use Illuminate\Database\Seeder;

class BookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BookCategory::insert([
            [
                'book_id' => 1,
                'category_id' => 1
            ],
            [
                'book_id' => 1,
                'category_id' => 2
            ],
            [
                'book_id' => 2,
                'category_id' => 3
            ]
        ]);
    }
}
