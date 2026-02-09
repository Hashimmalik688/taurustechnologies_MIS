<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements (admin page)
     */
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('admin.announcement.index', compact('announcements'));
    }

    /**
     * Show the form to create a new announcement
     */
    public function create()
    {
        $communities = \App\Models\Community::select('id', 'name')->orderBy('name')->get();
        return view('admin.announcement.create', compact('communities'));
    }

    /**
     * Store a new announcement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'animation' => 'required|in:slide,fade,bounce,wave',
            'background_color' => 'required|in:red,yellow,blue,green,purple,orange',
            'icon' => 'required|in:warning,info,important,star,check,alert',
            'auto_dismiss' => 'required|in:never,5s,10s,30s',
            'community_id' => 'nullable|exists:communities,id',
            'publish_now' => 'boolean',
        ]);

        // If community is selected, force icon to 'important' and background to 'red'
        if ($request->filled('community_id')) {
            $validated['icon'] = 'important';
            $validated['background_color'] = 'red';
        }

        // Deactivate any existing active announcements
        Announcement::where('is_active', true)->update(['is_active' => false]);

        // Create new announcement
        $announcement = Announcement::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'animation' => $validated['animation'],
            'background_color' => $validated['background_color'],
            'icon' => $validated['icon'],
            'auto_dismiss' => $validated['auto_dismiss'],
            'community_id' => $validated['community_id'] ?? null,
            'is_active' => true,
            'created_by' => Auth::id(),
            'published_at' => $request->publish_now ? now() : now()->addHours(1),
        ]);

        return redirect()->route('admin.announcements.index')
                       ->with('success', 'Announcement created successfully!');
    }

    /**
     * Show the form to edit an announcement
     */
    public function edit(Announcement $announcement)
    {
        $communities = \App\Models\Community::select('id', 'name')->orderBy('name')->get();
        return view('admin.announcement.edit', compact('announcement', 'communities'));
    }

    /**
     * Update an announcement
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'animation' => 'required|in:slide,fade,bounce,wave',
            'background_color' => 'required|in:red,yellow,blue,green,purple,orange',
            'icon' => 'required|in:warning,info,important,star,check,alert',
            'auto_dismiss' => 'required|in:never,5s,10s,30s',
            'community_id' => 'nullable|exists:communities,id',
            'is_active' => 'boolean',
        ]);

        // If community is selected, force icon to 'important' and background to 'red'
        if ($request->filled('community_id')) {
            $validated['icon'] = 'important';
            $validated['background_color'] = 'red';
        }

        // If activating this announcement, deactivate others
        if ($request->is_active) {
            Announcement::where('id', '!=', $announcement->id)
                       ->where('is_active', true)
                       ->update(['is_active' => false]);
        }

        $announcement->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'animation' => $validated['animation'],
            'background_color' => $validated['background_color'],
            'icon' => $validated['icon'],
            'community_id' => $validated['community_id'] ?? null,
            'is_active' => $request->is_active ?? false,
            'published_at' => $announcement->published_at ?? now(),
        ]);

        // Return JSON for AJAX requests, redirect for form submissions
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully!',
                'announcement' => $announcement,
            ]);
        }

        return back()->with('success', 'Announcement updated successfully!');
    }

    /**
     * Toggle announcement active status
     */
    public function toggle(Request $request, Announcement $announcement)
    {
        if ($request->is_active) {
            // Deactivate others
            Announcement::where('id', '!=', $announcement->id)
                       ->update(['is_active' => false]);
            $announcement->update(['is_active' => true, 'published_at' => now()]);
            return response()->json(['success' => true, 'message' => 'Announcement activated']);
        } else {
            $announcement->update(['is_active' => false]);
            return response()->json(['success' => true, 'message' => 'Announcement deactivated']);
        }
    }

    /**
     * Delete an announcement
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Get the current active announcement (API endpoint)
     */
    public function getCurrent()
    {
        $announcement = Announcement::getCurrent();
        if (!$announcement) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $announcement->id,
            'title' => $announcement->title,
            'message' => $announcement->message,
            'animation' => $announcement->animation,
            'background_color' => $announcement->background_color,
            'icon' => $announcement->icon,
            'auto_dismiss' => $announcement->auto_dismiss,
            'animation_class' => $announcement->getAnimationClass(),
            'background_class' => $announcement->getBackgroundClass(),
            'icon_class' => $announcement->getIconClass(),
        ]);
    }
}
