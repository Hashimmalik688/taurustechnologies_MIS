<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin|Manager'); // Only admins and managers can manage holidays
    }

    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->paginate(20);
        $upcomingHolidays = Holiday::upcoming(10);

        return view('admin.holidays.index', compact('holidays', 'upcomingHolidays'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');

        Holiday::create($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday added successfully!');
    }

    public function edit(Holiday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday updated successfully!');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday deleted successfully!');
    }

    /**
     * API endpoint to check if a date is a holiday
     */
    public function checkDate(Request $request)
    {
        $date = $request->input('date');
        $isHoliday = Holiday::isHoliday($date);

        return response()->json([
            'is_holiday' => $isHoliday,
            'holiday' => $isHoliday ? Holiday::where('date', Carbon::parse($date)->toDateString())->first() : null,
        ]);
    }
}
