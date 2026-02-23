<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\StickyNote;
use App\Models\User;
use Illuminate\Http\Request;

class ChatShadowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the chat shadowing page.
     */
    public function index()
    {
        return view('admin.settings.chat-shadow');
    }

    /**
     * Get all sticky notes for shadowing (read-only).
     */
    public function getNotes(Request $request)
    {
        $query = StickyNote::withTrashed()
            ->with('user:id,name')
            ->orderBy('updated_at', 'desc');

        // Filter by user
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Search in content
        if ($request->filled('search')) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        // Filter by status (active/deleted)
        if ($request->filled('status') && $request->status === 'deleted') {
            $query->whereNotNull('deleted_at');
        } elseif ($request->filled('status') && $request->status === 'active') {
            $query->whereNull('deleted_at');
        }

        $notes = $query->get();

        // Get all users who have notes (including deleted notes) for the filter dropdown
        $usersWithNotes = StickyNote::withTrashed()
            ->with('user:id,name')
            ->select('user_id')
            ->distinct()
            ->get()
            ->map(fn($n) => [
                'id' => $n->user_id,
                'name' => $n->user?->name ?? 'Deleted User',
            ])
            ->sortBy('name')
            ->values();

        $deletedCount = StickyNote::onlyTrashed()->count();

        $notes->transform(function ($note) {
            return [
                'id' => $note->id,
                'content' => $note->content,
                'color' => $note->color,
                'user_id' => $note->user_id,
                'user_name' => $note->user?->name ?? 'Deleted User',
                'is_deleted' => $note->trashed(),
                'deleted_at' => $note->deleted_at?->format('M d, Y h:i A'),
                'deleted_ago' => $note->deleted_at?->diffForHumans(),
                'created_at' => $note->created_at->format('M d, Y h:i A'),
                'updated_at' => $note->updated_at->format('M d, Y h:i A'),
                'created_ago' => $note->created_at->diffForHumans(),
                'updated_ago' => $note->updated_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'notes' => $notes,
            'users' => $usersWithNotes,
            'total' => $notes->count(),
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Get all conversations with search/filter support.
     */
    public function getConversations(Request $request)
    {
        $query = ChatConversation::withTrashed()
            ->with([
                'users' => fn($q) => $q->withTrashed(),
                'latestMessage' => fn($q) => $q->withTrashed(),
                'latestMessage.user',
                'creator',
            ])
            ->withCount(['messages' => fn($q) => $q->withTrashed()])
            ->latest('updated_at');

        // Filter by conversation type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter: deleted only
        if ($request->filled('status') && $request->status === 'deleted') {
            $query->whereNotNull('deleted_at');
        } elseif ($request->filled('status') && $request->status === 'active') {
            $query->whereNull('deleted_at');
        }

        // Search by participant name or conversation name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('users', function ($uq) use ($search) {
                        $uq->withTrashed()->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $conversations = $query->paginate(25);

        $conversations->getCollection()->transform(function ($conversation) {
            $participants = $conversation->users->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->avatar ? asset($u->avatar) : null,
            ]);

            $deletedMsgCount = ChatMessage::onlyTrashed()
                ->where('conversation_id', $conversation->id)
                ->count();

            return [
                'id' => $conversation->id,
                'name' => $conversation->name,
                'type' => $conversation->type,
                'is_deleted' => $conversation->trashed(),
                'deleted_at' => $conversation->deleted_at?->format('M d, Y h:i A'),
                'participants' => $participants,
                'display_name' => $conversation->type === 'direct'
                    ? $participants->pluck('name')->implode(' & ')
                    : ($conversation->name ?? 'Unnamed Group'),
                'messages_count' => $conversation->messages_count,
                'deleted_messages_count' => $deletedMsgCount,
                'latest_message' => $conversation->latestMessage ? [
                    'message' => $conversation->latestMessage->message,
                    'user_name' => $conversation->latestMessage->user?->name ?? 'Unknown',
                    'created_at' => $conversation->latestMessage->created_at->diffForHumans(),
                    'created_at_full' => $conversation->latestMessage->created_at->format('M d, Y h:i A'),
                    'is_deleted' => $conversation->latestMessage->trashed(),
                ] : null,
                'created_at' => $conversation->created_at->format('M d, Y'),
                'updated_at' => $conversation->updated_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Get messages for a specific conversation (read-only shadowing).
     */
    public function getMessages(Request $request, $conversationId)
    {
        $conversation = ChatConversation::withTrashed()
            ->with(['users' => fn($q) => $q->withTrashed()])
            ->findOrFail($conversationId);

        $query = ChatMessage::withTrashed()
            ->where('conversation_id', $conversationId)
            ->with(['user', 'attachments'])
            ->orderBy('created_at', 'asc');

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search within messages
        if ($request->filled('search')) {
            $query->where('message', 'like', "%{$request->search}%");
        }

        // Filter: show only deleted messages
        if ($request->filled('show_deleted') && $request->show_deleted === 'only') {
            $query->whereNotNull('deleted_at');
        }

        $messages = $query->paginate(100);

        // Append deleted flag to each message
        $messages->getCollection()->transform(function ($message) {
            $message->is_deleted = $message->trashed();
            $message->deleted_at_formatted = $message->deleted_at?->format('M d, Y h:i A');
            return $message;
        });

        $participants = $conversation->users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'avatar' => $u->avatar ? asset($u->avatar) : null,
        ]);

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'name' => $conversation->name,
                'type' => $conversation->type,
                'is_deleted' => $conversation->trashed(),
                'display_name' => $conversation->type === 'direct'
                    ? $participants->pluck('name')->implode(' & ')
                    : ($conversation->name ?? 'Unnamed Group'),
                'participants' => $participants,
            ],
            'messages' => $messages,
        ]);
    }
}
