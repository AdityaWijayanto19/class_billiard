<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        return response()->view('sitemap', [
            'pages' => [
                ['url' => '/', 'priority' => '1.0'],
                ['url' => '/menu', 'priority' => '0.8'],
            ]
        ])->header('Content-Type', 'text/xml');
    }
}   