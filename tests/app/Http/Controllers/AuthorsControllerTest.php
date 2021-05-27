<?php

namespace Tests\App\Http\Controllers;

use TestCase;

use Illuminate\Http\Response;

use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthorsControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function index_responds_with_200_status_code()
    {
        $this->get('/authors')->seeStatusCode(Response::HTTP_OK);
    }

    /** @test **/
    public function index_should_return_a_collection_of_records()
    {
        $authors = factory(\App\Author::class, 2)->create();

        $this->get('/authors', ['Accept' => 'application/json']);

        $body = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
        $this->assertCount(2, $body['data']);

        foreach ($authors as $author) {
            $this->seeJson([
                'id' => $author->id,
                'name' => $author->name,
                'gender' => $author->gender,
                'biography' => $author->biography,
                'created' => $author->created_at->toIso8601String(),
                'updated' => $author->updated_at->toIso8601String(),
            ]);
        }
    }

    /** @test **/
    public function show_should_return_a_valid_author()
    {
        $book = $this->bookFactory();
        $author = $book->author;

        $this->get("/authors/{$author->id}", ['Accept' => 'application/json']);

        $body = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $body);

        $this->seeJson([
            'id' => $author->id,
            'name' => $author->name,
            'gender' => $author->gender,
            'biography' => $author->biography,
            'created' => $author->created_at->toIso8601String(),
            'updated' => $author->updated_at->toIso8601String(),
        ]);
    }

    /** @test **/

    public function show_should_fail_on_an_invalid_author()
    {
        $this->get('/authors/1234', ['Accept' => 'application/json']);

        $this->seeStatusCode(Response::HTTP_NOT_FOUND);

        $this->seeJson([
            'message' => 'Not Found',
            'status' => Response::HTTP_NOT_FOUND
        ]);

        $body = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('error', $body);
        $error = $body['error'];

        $this->assertEquals('Not Found', $error['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $error['status']);
    }

    /** @test **/
    public function show_optionally_includes_books()
    {
        $book = $this->bookFactory();
        $author = $book->author;

        $this->get(
            "/authors/{$author->id}?include=books",
            ['Accept' => 'application/json']
        );

        $body = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];
        $this->assertArrayHasKey('books', $data);
        $this->assertArrayHasKey('data', $data['books']);
        $this->assertCount(1, $data['books']['data']);

        // See Author Data
        $this->seeJson([
            'id' => $author->id,
            'name' => $author->name,
        ]);

        // Test included book Data (the first record)
        $actual = $data['books']['data'][0];
        $this->assertEquals($book->id, $actual['id']);
        $this->assertEquals($book->title, $actual['title']);
        $this->assertEquals($book->description, $actual['description']);
        $this->assertEquals(
            $book->created_at->toIso8601String(),
            $actual['created']
        );
        $this->assertEquals(
            $book->updated_at->toIso8601String(),
            $actual['updated']
        );
    }

    /** @test **/
    public function store_can_create_a_new_author()
    {
        $postData = [
            'name' => 'H. G. Wells',
            'gender' => 'male',
            'biography' => 'Prolific Science-Fiction Writer',
        ];

        $this->post('/authors', $postData, ['Accept' => 'application/json']);

        $this->seeStatusCode(201);
        $data = $this->response->getData(true);
        $this->assertArrayHasKey('data', $data);
        $this->seeJson($postData);
        $this->seeInDatabase('authors', $postData);
    }

    /** @test **/
    public function store_method_validates_required_fields()
    {
        $this->post(
            '/authors',
            [],
            ['Accept' => 'application/json']
        );

        $data = $this->response->getData(true);

        $fields = ['name', 'gender', 'biography'];

        foreach ($fields as $field) {
            $this->assertArrayHasKey($field, $data);
            $this->assertEquals(["The {$field} field is required."], $data[$field]);
        }
    }

    /** @test **/
    public function store_invalidates_incorrect_gender_data()
    {
        $postData = [
            'name' => 'John Doe',
            'gender' => 'unknown',
            'biography' => 'An anonymous author'
        ];

        $this->post('/authors', $postData, ['Accept' => 'application/json']);

        $this->seeStatusCode(422);
        $data = $this->response->getData(true);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('gender', $data);
        $this->assertEquals(
            ["Gender format is invalid: must equal 'male' or 'female'"],
            $data['gender']
        );
    }

    /** @test **/
    public function store_invalidates_name_when_name_is_just_too_long()
    {
        $postData = [
            'name' => str_repeat('a', 256),
            'gender' => 'male',
            'biography' => 'A Valid Biography'
        ];

        $this->post('/authors', $postData, ['Accept' => 'application/json']);

        $this->seeStatusCode(422);
        $data = $this->response->getData(true);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertEquals(
            ["The name may not be greater than 255 characters."],
            $data['name']
        );
    }

    /** @test **/
    public function store_is_valid_when_name_is_just_long_enough()
    {
        $postData = [
            'name' => str_repeat('a', 255),
            'gender' => 'male',
            'biography' => 'A Valid Biography'
        ];

        $this->post('/authors', $postData, ['Accept' => 'application/json']);
        
        $this->seeStatusCode(201);
        $this->seeInDatabase('authors', $postData);
    }
}
