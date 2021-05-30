<?php

namespace Tests\App\Http\Controllers;

use TestCase;

use Laravel\Lumen\Testing\DatabaseMigrations;

class BundlesControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function show_should_return_a_valid_bundle()
    {
        $bundle = $this->bundleFactory();

        $this->get("/bundles/{$bundle->id}", ['Accept' => 'application/json']);

        $this->seeStatusCode(200);
        $body = $this->response->getData(true);
        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];

        // Check bundle properties exist in the response
        $this->assertEquals($bundle->id, $data['id']);
        $this->assertEquals($bundle->title, $data['title']);
        $this->assertEquals($bundle->title, $data['title']);
        $this->assertEquals(
            $bundle->description,
            $data['description']
        );
        $this->assertEquals(
            $bundle->created_at->toIso8601String(),
            $data['created']
        );
        $this->assertEquals(
            $bundle->updated_at->toIso8601String(),
            $data['updated']
        );

        // Check that book data is in the response
        $this->assertArrayHasKey('books', $data);
        $books = $data['books'];

        // Check that two books exist in the response
        $this->assertArrayHasKey('data', $books);
        $this->assertCount(2, $books['data']);

        // Verify keys for one book...
        $this->assertEquals(
            $bundle->books[0]->title,
            $books['data'][0]['title']
        );
        $this->assertEquals(
            $bundle->books[0]->description,
            $books['data'][0]['description']
        );
        $this->assertEquals(
            $bundle->books[0]->author->name,
            $books['data'][0]['author']
        );
        $this->assertEquals(
            $bundle->books[0]->created_at->toIso8601String(),
            $books['data'][0]['created']
        );
        $this->assertEquals(
            $bundle->books[0]->updated_at->toIso8601String(),
            $books['data'][0]['updated']
        );
    }
}
