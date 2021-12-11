<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class AboutController extends Controller
{

    public function test(): Response
    {
        return new Response("Api resources project. Please use /api/resources to manage your resources.");
    }
}
