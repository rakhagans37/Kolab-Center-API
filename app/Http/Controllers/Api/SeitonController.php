<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seiton;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SeitonController extends Controller
{
    public function scoreSeiton(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'score' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        // Ambil nilai score yang tervalidasi
        $score = $validator->validated()['score'];

        $user = User::find(Auth::id());

        // Cek apakah user sudah membuat seiton hari ini
        $hasSeitonToday = Seiton::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        // Tambahkan poin ke user hanya jika user belum membuat seiton hari ini
        if (!$hasSeitonToday) {
            // Buat record seiton baru dan kaitkan dengan user
            $seiton = new Seiton();
            $seiton->score = $score;
            $seiton->user_id = $user->id; // pastikan kolom user_id ada di tabel seitons
            $seiton->save();

            $user->points = $user->points + $score;
            $user->save();
        }

        return response()->json([
            'message' => 'Seiton recorded successfully',
            'data' => [
                'points' => $score,
                'user' => $user,
                'seiton' => $seiton ?? null,
                'pointsAdded' => !$hasSeitonToday
            ]
        ], 200);
    }
}
