<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $attendances,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:check_in,check_out',
        ]);

        $type = $request->type;
        // Gunakan waktu WITA (Asia/Makassar) secara mutlak
        $now = Carbon::now('Asia/Makassar');

        $attendance = Attendance::firstOrNew([
            'user_id' => $request->user()->id,
            'date' => $now->toDateString(),
        ]);

        if ($type === 'check_in' && ! $attendance->check_in) {
            $attendance->check_in = $now->format('H:i:s');
            $attendance->status = 'pending';
            $attendance->location = $request->location ?? 'WFO - Kantor Pusat';

            $isFriday = $now->isFriday();
            $targetMasukHour = $isFriday ? 7 : 8;
            $targetMasukMinute = $isFriday ? 30 : 0;
            $targetMasukStr = $isFriday ? '07:30:00' : '08:00:00';

            $jamMasuk = Carbon::createFromTime($targetMasukHour, $targetMasukMinute, 0, 'Asia/Makassar');
            if ($now->format('H:i:s') <= $targetMasukStr) {
                $attendance->notes = 'Tepat Waktu';
            } else {
                $selisih = (int) round(abs($now->diffInMinutes($jamMasuk)));
                $attendance->notes = 'Terlambat '.$selisih.' menit';
            }
        } elseif ($type === 'check_out' && ! $attendance->check_out) {
            $attendance->check_out = $now->format('H:i:s');
            $isFriday = $now->isFriday();
            $targetPulangStr = $isFriday ? '16:30:00' : '17:00:00';
            if ($now->format('H:i:s') < $targetPulangStr) {
                $attendance->notes = 'Terlalu Cepat Pulang';
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance already recorded for this type today.',
            ], 400);
        }

        $attendance->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance recorded successfully',
            'data' => $attendance,
        ]);
    }
}
