<?php

namespace Tests\App\Http\Controllers;

use TestCase;

use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthorsRatingsControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function store_can_add_a_rating_to_an_author()
    {
        $author = factory(\App\Author::class)->create();

        $this->post(
            "/authors/{$author->id}/ratings",
            ['value' => 5],
            ['Accept' => 'application/json']
        );
        $this->seeStatusCode(201)
            ->seeJson([
                'value' => 5
            ])
            ->seeJson([
                'rel' => 'author',
                'href' => route('authors.show', ['id' => $author->id])
            ]);

        $body = $this->response->getData(true);
        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];
        $this->assertArrayHasKey('links', $data);
    }
}
