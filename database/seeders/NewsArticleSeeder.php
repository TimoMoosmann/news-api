<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NewsArticle::create([
            'title' => 'Probetag bei Workwise',
            'author' => 'Timo',
            'text' => <<<TEXT
                Mit dem Zug nach Karlsruhe...
                Laravel API schreiben...
                TEXT,
            'publication_date' => '21.12.2002',
        ]);
    }
}
