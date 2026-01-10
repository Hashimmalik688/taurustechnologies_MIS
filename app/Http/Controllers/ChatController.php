<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatAttachment;
use App\Models\ChatParticipant;
use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the chat page
     */
    public function index()
    {
        return view('chat.index');
    }

    /**
     * Get all conversations for the authenticated user
     */
    public function getConversations()
    {
        $userId = Auth::id();

        $conversations = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['latestMessage.user', 'users', 'participants' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                // Get other participants (for direct chats, show the other person's name)
                $otherUsers = $conversation->users->filter(fn($user) => $user->id !== $userId);

                return [
                    'id' => $conversation->id,
                    'name' => $conversation->type === 'group'
                        ? $conversation->name 
                        : ($otherUsers->first()->name ?? 'Unknown User'),
                    'type' => $conversation->type,
                    'avatar' => $conversation->type === 'direct'
                        ? $otherUsers->first()->avatar ?? null
                        : null,
                    'latest_message' => $conversation->latestMessage ? [
                        'message' => $conversation->latestMessage->message,
                        'created_at' => $conversation->latestMessage->created_at->diffForHumans(),
                        'user_name' => $conversation->latestMessage->user->name,
                    ] : null,
                    'unread_count' => $conversation->unreadCount($userId),
                    'updated_at' => $conversation->updated_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Get or create a direct conversation with another user
     */
    public function getOrCreateConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = Auth::id();
        $otherUserId = $request->user_id;

        // Check if conversation already exists
        $conversation = ChatConversation::where('type', 'direct')
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('participants', function ($query) use ($otherUserId) {
                $query->where('user_id', $otherUserId);
            })
            ->first();

        if (!$conversation) {
            // If not, create new conversation within a transaction
            $conversation = DB::transaction(function () use ($userId, $otherUserId) {
                $conversation = ChatConversation::create([
                    'type' => 'direct',
                    'created_by' => $userId,
                ]);

                // Add both participants with last_read_at set to now
                $conversation->participants()->createMany([
                    ['user_id' => $userId, 'last_read_at' => now()],
                    ['user_id' => $otherUserId, 'last_read_at' => now()],
                ]);
                return $conversation;
            });
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
        ]);
    }

    /**
     * Create a group conversation
     */
    public function createGroupConversation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $conversation = DB::transaction(function () use ($request) {
            $creatorId = Auth::id();
            $conversation = ChatConversation::create([
                'name' => $request->name,
                'type' => 'group',
                'created_by' => $creatorId,
            ]);

            $participantIds = collect($request->user_ids)->push($creatorId)->unique();

            foreach ($participantIds as $userId) {
                $conversation->participants()->create([
                    'user_id' => $userId,
                    'last_read_at' => now(),
                ]);
            }
            return $conversation;
        });

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
        ]);
    }

    /**
     * Create a group conversation
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userId = Auth::id();
        $userIds = $request->user_ids;
        
        // Add current user to the group if not already included
        if (!in_array($userId, $userIds)) {
            $userIds[] = $userId;
        }

        $conversation = DB::transaction(function () use ($request, $userIds, $userId) {
            // Create group conversation
            $conversation = ChatConversation::create([
                'name' => $request->name,
                'type' => 'group',
                'created_by' => $userId,
            ]);

            // Add all participants
            foreach ($userIds as $participantId) {
                ChatParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $participantId,
                    'joined_at' => now(),
                    'last_read_at' => now(),
                ]);
            }

            return $conversation;
        });

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'conversation' => $conversation,
        ]);
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages($conversationId)
    {
        $userId = Auth::id();

        // Verify user is a participant
        $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($conversationId);

        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->with(['user', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark as read
        $participant = ChatParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();

        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'conversation' => [
                'id' => $conversation->id,
                'name' => $conversation->name,
                'type' => $conversation->type,
            ],
        ]);
    }

    /**
     * Send a text message
     */
    public function sendMessage(Request $request)
    {
        // Log the incoming request data for debugging
        \Log::info('SendMessage Request Data', [
            'conversation_id' => $request->conversation_id,
            'message' => $request->message,
            'has_attachments' => $request->hasFile('attachments'),
            'all_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        try {
            $request->validate([
                'conversation_id' => 'required|exists:chat_conversations,id',
                'message' => 'nullable|string|max:5000', // Changed to nullable for files-only messages
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|max:10240', // 10MB max per file
            ]);
            
            // Additional validation: require either message or attachments
            if (empty($request->message) && (!$request->hasFile('attachments') || count($request->file('attachments')) === 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either message text or file attachments are required.'
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('SendMessage Validation Failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            throw $e;
        }

        $userId = Auth::id();

        // Verify user is a participant
        $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($request->conversation_id);

        // Determine message type (support audio/images/files)
        $type = 'text';
        if ($request->hasFile('attachments')) {
            $firstFile = $request->file('attachments')[0];
            $mime = $firstFile->getMimeType();
            if (str_starts_with($mime, 'audio/')) {
                $type = 'audio';
            } elseif (str_starts_with($mime, 'image/')) {
                $type = 'image';
            } else {
                $type = 'file';
            }
        }

        $message = DB::transaction(function () use ($request, $userId, $type, $conversation) {
            // Create message
            $message = ChatMessage::create([
                'conversation_id' => $request->conversation_id,
                'user_id' => $userId,
                'message' => $request->message,
                'type' => $type,
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('chat-attachments', 'public');

                    ChatAttachment::create([
                        'message_id' => $message->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->extension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            // Update conversation timestamp
            $conversation->touch();

            return $message;
        });


        // Load relationships for response
        $message->load(['user', 'attachments']);

        // Broadcast event for real-time updates. This will broadcast the newly created
        // message to other participants on the private conversation channel.
        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            // If broadcasting isn't configured yet (no pusher/websockets), don't fail the
            // request — log silently. This keeps behavior safe while we wire up the
            // broadcasting driver and front-end Echo listeners.
            report($e);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Delete a message
     */
    public function deleteMessage($messageId)
    {
        $message = ChatMessage::where('user_id', Auth::id())
            ->findOrFail($messageId);

        DB::transaction(function () use ($message) {
            // Delete attachments from storage
            foreach ($message->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $message->delete(); // This will also delete related attachments from DB via cascade
        });

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }

    /**
     * Get all users for starting a new chat
     */
    public function getUsers()
    {
        $users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    /**
     * Search conversations and messages
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $userId = Auth::id();
        $query = $request->query;

        // Search in user's conversations
        $conversations = ChatConversation::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhereHas('users', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    });
            })
            ->with(['latestMessage', 'users'])
            ->get();

        // Search in messages
        $messages = ChatMessage::whereHas('conversation.participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('message', 'like', "%{$query}%")
            ->with(['conversation', 'user'])
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
            'messages' => $messages,
        ]);
    }

    /**
     * Get a specific conversation
     */
    public function getConversation($id)
    {
        $userId = Auth::id();

        $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['users'])
            ->findOrFail($id);

        // Get creator information
        $creator = User::find($conversation->created_by);

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'name' => $conversation->name,
                'type' => $conversation->type,
                'created_by' => $conversation->created_by,
                'creator' => $creator ? ['id' => $creator->id, 'name' => $creator->name] : null,
                'created_at' => $conversation->created_at,
            ]
        ]);
    }

    /**
     * Update conversation (e.g., change name)
     */
    public function updateConversation($id, Request $request)
    {
        $userId = Auth::id();

        $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($id);

        // Only creator can update group conversations
        if ($conversation->type === 'group' && $conversation->created_by !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Only the group creator can update the group.'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $conversation->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $conversation
        ]);
    }

    /**
     * Delete conversation
     */
    public function deleteConversation($id)
    {
        $userId = Auth::id();

        $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($id);

        // Only creator can delete group conversations
        if ($conversation->type === 'group' && $conversation->created_by !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Only the group creator can delete the group.'
            ], 403);
        }

        DB::transaction(function () use ($conversation) {
            // Delete all messages and their attachments
            $messages = ChatMessage::where('conversation_id', $conversation->id)->get();
            foreach ($messages as $message) {
                // Delete attachments from storage
                foreach ($message->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
            }

            // Delete conversation (cascades to messages, participants, attachments)
            $conversation->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted successfully'
        ]);
    }

    /**
     * Get conversation members
     */
    public function getConversationMembers($id)
    {
        $userId = Auth::id();

        $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($id);

        $members = User::whereIn('id', function ($query) use ($id) {
            $query->select('user_id')
                ->from('chat_participants')
                ->where('conversation_id', $id);
        })
            ->select('id', 'name', 'email')
            ->get();

        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }

    /**
     * Add member to group conversation
     */
    public function addMember($id, Request $request)
    {
        $userId = Auth::id();

        $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($id);

        // Only creator can add members to group conversations
        if ($conversation->type === 'group' && $conversation->created_by !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Only the group creator can add members.'
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $newUserId = $request->user_id;

        // Check if user is already a member
        $existingParticipant = ChatParticipant::where('conversation_id', $id)
            ->where('user_id', $newUserId)
            ->first();

        if ($existingParticipant) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a member of this group.'
            ], 400);
        }

        // Add the user as a participant
        ChatParticipant::create([
            'conversation_id' => $id,
            'user_id' => $newUserId,
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member added successfully'
        ]);
    }

    /**
     * Remove member from group conversation
     */
    public function removeMember($id, $userId)
    {
        $currentUserId = Auth::id();

        $conversation = ChatConversation::whereHas('participants', function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId);
        })->findOrFail($id);

        // Only creator can remove members from group conversations
        if ($conversation->type === 'group' && $conversation->created_by !== $currentUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Only the group creator can remove members.'
            ], 403);
        }

        // Cannot remove the group creator
        if ($userId == $conversation->created_by) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the group creator.'
            ], 400);
        }

        // Remove the participant
        $deleted = ChatParticipant::where('conversation_id', $id)
            ->where('user_id', $userId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this group.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Member removed successfully'
        ]);
    }

    /**
     * Get total unread message count for authenticated user
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();

        $totalUnread = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->get()
            ->sum(function ($conversation) use ($userId) {
                return $conversation->unreadCount($userId);
            });

        return response()->json([
            'success' => true,
            'unread_count' => $totalUnread,
        ]);
    }
}
