<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seiton;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function getRanking()
    {
        $ranking = User::orderBy('points', 'desc')->limit(5)->get();

        return response()->json([
            'data' => [
                'ranking' => $ranking,
            ]
        ], 200);
    }

    public function getSeitonRanking()
    {
        $ranking = Seiton::select('user_id', DB::raw('SUM(score) as total_score'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total_score', 'desc')
            ->get();

        return response()->json(
            [
                'message' => 'Success',
                'data' => [
                    'ranking' => $ranking
                ]
            ]
        );
    }
}
