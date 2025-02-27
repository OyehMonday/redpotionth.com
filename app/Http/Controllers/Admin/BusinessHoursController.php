<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessHour;

class BusinessHoursController extends Controller
{
    public function index()
    {
        $businessHours = BusinessHour::orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
        return view('admin.business-hours.index', compact('businessHours'));
    }

    public function update(Request $request)
    {
        foreach ($request->hours as $day => $times) {
            BusinessHour::where('day', $day)->update([
                'open_time' => $times['open_time'] ?? null,
                'close_time' => $times['close_time'] ?? null
            ]);
        }

        return redirect()->back()->with('success', 'Business hours updated successfully.');
    }
}
