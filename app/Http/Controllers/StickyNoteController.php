<?php

namespace App\Http\Controllers;

use App\Models\StickyNote;
use Illuminate\Http\Request;

class StickyNoteController extends Controller
{
    /**
     * Get all sticky notes for the authenticated user.
     */
    public function index()
    {
        $notes = auth()->user()->stickyNotes()->orderBy('z_index')->get();
        return response()->json($notes);
    }

    /**
     * Store a new sticky note.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'z_index' => 'nullable|integer',
        ]);

        $note = auth()->user()->stickyNotes()->create($validated);

        return response()->json([
            'success' => true,
            'note' => $note
        ]);
    }

    /**
     * Update an existing sticky note.
     */
    public function update(Request $request, StickyNote $stickyNote)
    {
        // Ensure user owns this note
        if ($stickyNote->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'z_index' => 'nullable|integer',
        ]);

        $stickyNote->update($validated);

        return response()->json([
            'success' => true,
            'note' => $stickyNote
        ]);
    }

    /**
     * Delete a sticky note.
     */
    public function destroy(StickyNote $stickyNote)
    {
        // Ensure user owns this note
        if ($stickyNote->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stickyNote->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
