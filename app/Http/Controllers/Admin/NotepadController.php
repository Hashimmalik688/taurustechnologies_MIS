<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotepadNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotepadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();

        $notes = NotepadNote::with('sharedWith:id')
            ->where('user_id', $userId)
            ->orderByDesc('is_pinned')
            ->orderByDesc('updated_at')
            ->get();

        $sharedNotes = NotepadNote::with('user:id,name')
            ->whereHas('sharedWith', fn($q) => $q->where('user_id', $userId))
            ->where('user_id', '!=', $userId)
            ->orderByDesc('updated_at')
            ->get();

        $shareableUsers = User::where('id', '!=', $userId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('notepad.index', compact('notes', 'sharedNotes', 'shareableUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'color'   => 'nullable|string|max:20',
        ]);

        $note = NotepadNote::create([
            'user_id'   => Auth::id(),
            'title'     => $request->title ?? 'Untitled',
            'content'   => $request->content ?? '',
            'color'     => $request->color ?? '#ffffff',
            'is_pinned' => false,
            'is_shared' => false,
        ]);

        return response()->json(['success' => true, 'note' => $this->formatNote($note)]);
    }

    public function update(Request $request, NotepadNote $note)
    {
        $userId  = Auth::id();
        $isOwner = $note->user_id === $userId;
        $isSharedEditor = !$isOwner && $note->sharedWith()->where('user_id', $userId)->exists();

        if (!$isOwner && !$isSharedEditor) {
            abort(403);
        }

        $request->validate([
            'title'     => 'nullable|string|max:255',
            'content'   => 'nullable|string',
            'color'     => 'nullable|string|max:20',
            'is_pinned' => 'nullable|boolean',
        ]);

        if ($isOwner) {
            $note->update($request->only(['title', 'content', 'color', 'is_pinned']));
        } else {
            // Shared editors can only modify content and title
            $note->update($request->only(['title', 'content']));
        }

        return response()->json(['success' => true, 'note' => $this->formatNote($note->fresh())]);
    }

    public function getShares(NotepadNote $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $sharedUserIds = $note->sharedWith()->pluck('user_id')->toArray();

        return response()->json(['success' => true, 'shared_user_ids' => $sharedUserIds]);
    }

    public function updateShares(Request $request, NotepadNote $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'user_ids'   => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $ids = collect($request->input('user_ids', []))
            ->map(fn($id) => (int)$id)
            ->filter(fn($id) => $id !== Auth::id())
            ->unique()
            ->values()
            ->toArray();

        $note->sharedWith()->sync($ids);
        $note->update(['is_shared' => count($ids) > 0]);

        return response()->json([
            'success'         => true,
            'shared_user_ids' => $ids,
            'is_shared'       => count($ids) > 0,
        ]);
    }

    /**
     * Poll a note's latest content (for real-time shared editing sync).
     * Returns current title, content, and updated_at so the client can
     * decide whether to refresh its local copy.
     */
    public function poll(NotepadNote $note)
    {
        $userId  = Auth::id();
        $isOwner = $note->user_id === $userId;
        $isSharedEditor = !$isOwner && $note->sharedWith()->where('user_id', $userId)->exists();

        if (!$isOwner && !$isSharedEditor) {
            abort(403);
        }

        return response()->json([
            'success'    => true,
            'id'         => $note->id,
            'title'      => $note->title,
            'content'    => $note->content,
            'updated_at' => $note->updated_at->toIso8601String(),
            'updated_ago' => $note->updated_at->diffForHumans(),
        ]);
    }

    public function destroy(NotepadNote $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $note->delete();

        return response()->json(['success' => true]);
    }

    private function formatNote(NotepadNote $note): array
    {
        return [
            'id'          => $note->id,
            'title'       => $note->title,
            'content'     => $note->content,
            'color'       => $note->color,
            'is_pinned'   => $note->is_pinned,
            'is_shared'   => $note->is_shared,
            'created_at'  => $note->created_at->format('M d, Y h:i A'),
            'updated_at'  => $note->updated_at->toIso8601String(),
            'updated_ago' => $note->updated_at->diffForHumans(),
        ];
    }
}
