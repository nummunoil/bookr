<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;
use App\Transformer\RatingTransformer;

/**
* Manage an Author's Ratings
*/
class AuthorsRatingsController extends Controller
{
    public function store(Request $request, $authorId)
    {
        $author = Author::findOrFail($authorId);

        $rating = $author->ratings()->create(['value' => $request->get('value')]);

        $data = $this->item($rating, new RatingTransformer());

        return response()->json($data, 201);
    }
}
