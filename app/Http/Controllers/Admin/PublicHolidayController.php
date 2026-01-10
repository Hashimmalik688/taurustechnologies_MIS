<?php

namespace App\Http\Controllers\Admin;

use App\Models\PublicHoliday;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class PublicHolidayController extends Controller
{
    public function index()
    {
        // Get holidays from both tables and merge them
        $newHolidays = PublicHoliday::orderBy('date', 'desc')->get();
        $legacyHolidays = Holiday::orderBy('date', 'desc')->get();
        
        // Merge and paginate
        $allHolidays = $newHolidays->merge($legacyHolidays)
            ->sortByDesc('date')
            ->unique(function($h) { return $h->date->format('Y-m-d'); });
        
        // Manual pagination
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $perPage = 20;
        $currentPageItems = $allHolidays->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $holidays = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allHolidays->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        // Get upcoming from both sources
        $upcomingNew = PublicHoliday::where('date', '>=', Carbon::today())->where('is_active', true)->orderBy('date')->limit(5)->get();
        $upcomingLegacy = Holiday::where('date', '>=', Carbon::today())->orderBy('date')->limit(5)->get();
        $upcomingHolidays = $upcomingNew->merge($upcomingLegacy)
            ->sortBy('date')
            ->unique(function($h) { return $h->date->format('Y-m-d'); })
            ->take(5);
        
        return view('admin.holidays.index', compact('holidays', 'upcomingHolidays'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:public_holidays,date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        PublicHoliday::create($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Public holiday added successfully.');
    }

    public function edit(PublicHoliday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, PublicHoliday $holiday)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:public_holidays,date,' . $holiday->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Public holiday updated successfully.');
    }

    public function destroy(PublicHoliday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Public holiday deleted successfully.');
    }

    public function toggle(PublicHoliday $holiday)
    {
        $holiday->update(['is_active' => !$holiday->is_active]);

        return back()->with('success', 'Holiday status updated successfully.');
    }

    /**
     * API endpoint to check if a date is a holiday
     */
    public function checkDate(Request $request)
    {
        $date = $request->input('date');
        $isHoliday = PublicHoliday::isHoliday($date);
        
        return response()->json([
            'is_holiday' => $isHoliday,
            'date' => $date,
        ]);
    }

    /**
     * Get holidays for a specific month (for calendar views)
     */
    public function getMonthHolidays(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        
        $holidays = PublicHoliday::getMonthHolidays($year, $month);
        
        return response()->json($holidays);
    }
}
