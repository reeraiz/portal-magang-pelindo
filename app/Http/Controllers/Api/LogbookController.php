<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $logbooks = Logbook::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $logbooks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $now = Carbon::now('Asia/Makassar');

        $logbook = Logbook::create([
            'user_id' => $request->user()->id,
            'date' => $now->toDateString(),
            'time' => $now->format('H:i:s'),
            'category' => $request->category,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Logbook recorded successfully',
            'data' => $logbook,
        ]);
    }
}
