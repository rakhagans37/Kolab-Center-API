<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function timeIn(Request $request)
    {
        $user = $request->user();

        // Cek jika user sudah melakukan check in (belum melakukan time out)
        if ($user->attendance()->whereNull('time_out')->exists()) {
            return response()->json([
                'message' => 'You are already time in',
            ], 400);
        }

        // Buat record attendance baru dengan waktu check in sekarang
        $attendance = $user->attendance()->create([
            'time_in' => now(),
        ]);

        // Ambil seluruh attendance hari ini (berdasarkan kolom time_in) secara ascending
        $todayAttendances = \App\Models\Attendance::whereDate('time_in', now()->toDateString())
            ->orderBy('time_in')
            ->get();

        // Cari urutan (rank) check in user ini (dimulai dari 1)
        $rank = $todayAttendances->search(function ($att) use ($attendance) {
            return $att->id === $attendance->id;
        }) + 1;

        // Hitung total check in hari ini
        $totalAttendances = $todayAttendances->count();

        // Definisikan poin maksimum dan minimum
        $maxPoints = 100;
        $minPoints = 50;

        // Jika hanya ada satu check in (user ini saja), berikan poin maksimum
        if ($totalAttendances === 1) {
            $points = $maxPoints;
        } else {
            // Hitung penurunan poin per urutan
            $decrement = ($maxPoints - $minPoints) / ($totalAttendances - 1);
            // Hitung poin untuk user berdasarkan urutan check in-nya
            $points = round($maxPoints - ($rank - 1) * $decrement);
        }

        // Update record attendance dengan poin yang sudah dihitung
        $user = User::find(Auth::id());
        $user->points = $user->points + $points;
        $user->save();

        return response()->json([
            'message' => 'Time in successfully',
            'data' => [
                'points' => $points,
                'user' => $user
            ]
        ], 200);
    }



    public function timeOut(Request $request)
    {
        $attendance = $request->user()->attendance()->latest()->first();

        if ($attendance) {
            return response()->json([
                'message' => 'You have not time in yet',
            ], 400);
        }

        $attendance->update([
            'time_out' => now(),
        ]);

        return response()->json([
            'message' => 'Time out successfully',
        ]);
    }
}
