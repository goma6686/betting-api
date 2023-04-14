<?php

namespace App\Http\Controllers;

use App\Models\Game;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::all();
        
        return view('welcome', ['games' => $games,]);
    }
}
