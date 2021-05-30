<?php

namespace App\Http\Controllers;

use App\Bundle;
use App\Transformer\BundleTransformer;

/**
* Class BundlesController
* @package App\Http\Controllers
*/
class BundlesController extends Controller
{
    public function show($id)
    {
        $bundle = Bundle::findOrFail($id);
        $data = $this->item($bundle, new BundleTransformer());

        return response()->json($data);
    }
}
