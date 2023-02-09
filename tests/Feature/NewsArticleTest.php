<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
                'message' => 'News Article with ID 1 was not found.',
            ]);
    }

    public function testCreateInitialArticles() {
        $response = $this->createTestArticle1();
        $this->expectCreated($response, 1);
        
        $response = $this->createTestArticle2();
        $this->expectCreated($response, 2);
    }

    public function testViewSingleNewsArticle() {
        $this->createTestArticle1();
        $this->createTestArticle2();

        $response = $this->getJson("$this->newsArticlesApiBasePath/1");
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('data.id', 1)
                ->where('data.title', $this->testArticle1Array['title'])
                ->where('data.author', $this->testArticle1Array['author'])
                ->missing('data.text')
                ->has('data.created_at')
                ->missing('data.published_at')
                ->etc()
        );

        $response = $this->getJson("$this->newsArticlesApiBasePath/1");
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('data.id', 2)
                ->where('data.title', $this->testArticle2Array['title'])
                ->where('data.author', $this->testArticle2Array['author'])
                ->where('data.text', $this->testArticle2Array['text'])
                ->has('data.created_at')
                ->has('data.published_at')
                ->etc()
        );
    }

    public function testGetNewsArticlesList() {
        $this->createTestArticle1();
        $this->createTestArticle2();

        $response = $this->getJson($this->newsArticlesApiBasePath);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has(2)
                ->first(fn ($json) =>
                    $json
                        ->where('data.id', 1)
                        ->where('data.title', $this->testArticle1Array['title'])
                        ->where('data.author', $this->testArticle1Array['author'])
                        ->missing('data.text')
                        ->has('data.created_at')
                        ->missing('data.published_at')
                        ->etc()
                )
        );
    }

    public function testUpdateNewsArticle() {
        $response = $this->putJson("$this->newsArticlesApiBasePath/1", [
            'title' => 'New Name',
            'publish' => true,
        ]);

        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('data.id', 1)
                ->where('data.title', 'New Title')
                ->where('data.author', $this->testArticle1Array['author'])
                ->where('data.text', $this->testArticle1Array['text'])
                ->has('data.created_at')
                ->has('data.published_at')
                ->etc()
        );
    }

    public function destroyNewsArticle() {
        $response = $this->deleteJson("$this->newsArticlesApiBasePath/1");

        $response->assertJson([
            'status' => 'success',
            'message' => 'Deleted News Article with ID 1',
        ]);

        $response = $this->getJson("$this->newsArticlesApiBasePath/1");
        $response->assertStatus(404);
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

    private function createTestArticle1(): TestResponse {
        return $this->postJson($this->newsArticlesApiBasePath, array_merge(
            $this->testArticle1Array,
            ['publish' => false,]
        ));
    }

    private function createTestArticle2(): TestResponse {
        return $this->postJson($this->newsArticlesApiBasePath, array_merge(
            $this->testArticle2Array,
            ['publish' => true]
        ));
    }
    private function expectCreated(TestResponse $response, int $expectedId) {
        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => "News Article with ID $expectedId created.",
            ]);
    }
}
