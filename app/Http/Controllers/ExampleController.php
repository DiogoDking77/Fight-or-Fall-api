<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function getData()
    {
        return response()->json(['message' => 'Hello from Laravel']);
    }
}

