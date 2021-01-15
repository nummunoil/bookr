<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Response\FractalResponse;
use App\Transformer\BookTransformer;

/**
* Class BooksController
* @package App\Http\Controllers
*/
class BooksController
{
    public function __construct(FractalResponse $fractal)
    {
        $this->fractal = $fractal;
    }


    /**
     * GET /books
     * @return array
     */
    public function index()
    {
        $books = Book::all();
        return $this->fractal->collection($books, new BookTransformer());
    }

    /**
    * GET /books/{id}
    * @param integer $id
    * @return mixed
    */
    public function show($id)
    {
        $book = Book::findOrFail($id);
        return $this->fractal->item($book, new BookTransformer());
    }

    /**
    * POST /books
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function store(Request $request)
    {
        $book = Book::create($request->all());

        return response()->json(['data' => $book->toArray()], 201, [
            'Location' => route('books.show', ['id' => $book->id])
        ]);
    }

    /**
    * PUT /books/{id}
    *
    * @param Request $request
    * @param $id
    * @return mixed
    */
    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }
            
        $book->fill($request->all());
        $book->save();
            
        return ['data' => $book->toArray()];
    }

    /**
    * DELETE /books/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found']
            ], 404);
        }
        
        $book->delete();

        return response(null, 204);
    }
}
