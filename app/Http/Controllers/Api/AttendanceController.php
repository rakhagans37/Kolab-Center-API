<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function timeIn(Request $request)
    {
        if ($request->user()->attendance()->whereNull('time_out')->exists()) {
            return response()->json([
                'message' => 'You are already time in',
            ], 400);
        }

        $request->user()->attendance()->create([
            'time_in' => now(),
        ]);

        return response()->json([
            'message' => 'Time in successfully',
        ]);
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
