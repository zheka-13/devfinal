<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function test(): Response
    {
        return new Response("hello");
    }
}
