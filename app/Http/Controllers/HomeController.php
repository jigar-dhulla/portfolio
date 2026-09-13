<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the portfolio.
     */
    public function __invoke(): View
    {
        return view('home');
    }
}
