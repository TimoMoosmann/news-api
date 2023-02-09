<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use \Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class NewsArticleTest extends TestCase
{
    use RefreshDatabase;

    private $newsArticlesApiBasePath = '/api/news-articles';

    private $testArticle1Array = [
        'title' => 'Probetag bei Workwise',
        'author' => 'Timo',
        'text' => <<<TEXT
            Mit dem Zug nach Karlsruhe...
            Laravel API schreiben...
            TEXT,
    ];
    private $testArticle2Array = [
        'title' => 'Programmiraufgabe Anweisungen',
        'author' => 'Tilman',
        'text' => <<<TEXT
            Laravel installieren...
            API erstellen...
            TEXT,
    ];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetNewsArticlesBeforeArticleCreation()
    {
        $response = $this->get($this->newsArticlesApiBasePath);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [],
            ]);
    }

    public function testGetSingleNewsArticleBeforeArticleCreation()
    {
        $response = $this->get("$this->newsArticlesApiBasePath/1");

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Article with ID 1 was not found.',
            ]);
    }

    public function testCreateInitialArticles() {
        $response = $this->postJson($this->newsArticlesApiBasePath, array_merge(
            $this->testArticle1Array,
            ['publish' => false,]
        ));
        $this->expectCreated($response, 1);
        

        $response = $this->postJson($this->newsArticlesApiBasePath, array_merge(
            $this->testArticle2Array,
            ['publish' => true]
        ));
        $this->expectCreated($response, 2);
    }

    public function testViewSingleNewsArticle() {
        $response = $this->getJson("$this->newsArticlesApiBasePath/1");
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('id', 1)
                ->where('title', $this->testArticle1Array['title'])
                ->where('author', $this->testArticle1Array['author'])
                ->where('text', $this->testArticle1Array['text'])
                ->has('creation_date')
                ->missing('publication_date')
                ->etc()
        );

        $response = $this->getJson("$this->newsArticlesApiBasePath/1");
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('id', 2)
                ->where('title', $this->testArticle2Array['title'])
                ->where('author', $this->testArticle2Array['author'])
                ->where('text', $this->testArticle2Array['text'])
                ->has('creation_date')
                ->has('publication_date')
                ->etc()
        );
    }

    public function testGetNewsArticlesList() {
        $response = $this->getJson("$this->newsArticlesApiBasePath");
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has(2)
                ->first(fn ($json) =>
                    $json
                        ->where('id', 1)
                        ->where('title', $this->testArticle1Array['title'])
                        ->where('author', $this->testArticle1Array['author'])
                        ->where('text', $this->testArticle1Array['text'])
                        ->has('creation_date')
                        ->missing('publication_date')
                        ->etc()
                )
        );
    }

    /*
    public function testCreateIllegalArticlesShouldFail() {
        $response = $this->postJson($this->newsArticlesApiBasePath, [
            'title' =>
            'blablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablabl',
            'author' => 'Timo',
            'text' => <<<TEXT
                Test Text Test Text Test Text
                TEXT,
        ]);
        $response->assertStatus(400);
        // Assert Title too long

        // Test cases: no title / author / text
        // title too short.
        // text too short.

        // todo Aufgabe 1.2: Erweitern
    }
    */

    // testViews after creation
    // test single View after Creation
    // test update
    // test destroy

    private function expectCreated(TestResponse $response, int $expectedId) {
        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => "News Article with ID $expectedId created.",
            ]);
    }
}
