<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function getRanking(){
        $ranking = User::orderBy('points', 'desc')->limit(5)->get();

        return response()->json([
            'ranking' => $ranking,
        ]);
    }
}
