<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Отображает приветственную страницу.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('welcome');
    }
}
