<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;
    
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
    * See if the response has a header.
    *
    * @param $header
    * @return $this
    */
    public function seeHasHeader($header)
    {
        $this->assertTrue(
            $this->response->headers->has($header),
            "Response should have the header '{$header}' but does not."
        );
        return $this;
    }

    /**
    * Asserts that the response header matches a given regular expression
    *
    * @param $header
    * @param $regexp
    * @return $this
    */
    public function seeHeaderWithRegExp($header, $regexp)
    {
        $this->seeHasHeader($header)
            ->assertRegExp(
                $regexp,
                $this->response->headers->get($header)
            );
            
        return $this;
    }

    /**
    * Convenience method for creating a book with an author
    *
    * @param int $count
    * @return mixed
    */
    protected function bookFactory($count = 1)
    {
        $author = factory(\App\Author::class)->create();
        $books = factory(\App\Book::class, $count)->make();

        $books->each(function ($book) use ($author) {
            $book->author()->associate($author);
            $book->save();
        });

        return $count == 1 ? $books->first() : $books;
    }
}
