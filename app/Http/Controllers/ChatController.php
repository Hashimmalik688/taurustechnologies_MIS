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
            ->where('type', 'direct') // Only show direct conversations in Messages list
            ->with(['latestMessage.user', 'users', 'participants' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                // Get other participants (for direct chats, show the other person's name)
                $otherUsers = $conversation->users->filter(fn($user) => $user->id !== $userId);
                $otherUser = $otherUsers->first();

                // Format avatar URL properly
                $avatarUrl = null;
                if ($conversation->type === 'direct' && $otherUser && $otherUser->avatar) {
                    // Use asset() to generate proper URL
                    $avatarUrl = asset($otherUser->avatar);
                } elseif ($conversation->type === 'group' && $conversation->avatar) {
                    $avatarUrl = asset($conversation->avatar); // Group avatar path
                }

                return [
                    'id' => $conversation->id,
                    'name' => $conversation->type === 'group'
                        ? $conversation->name 
                        : ($otherUser->name ?? 'Unknown User'),
                    'type' => $conversation->type,
                    'community_id' => $conversation->community_id,
                    'avatar' => $avatarUrl,
                    'latest_message' => $conversation->latestMessage ? [
                        'message' => $conversation->latestMessage->message,
                        'created_at' => $conversation->latestMessage->created_at->diffForHumans(),
                        'user_name' => $conversation->latestMessage->user?->name ?? 'Unknown',
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
     * Get group conversations for the authenticated user (for Communities tab)
     */
    public function getGroupConversations()
    {
        $userId = Auth::id();

        $conversations = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('type', 'group') // Only group conversations
            ->whereNull('community_id') // Exclude community groups
            ->with(['latestMessage.user', 'users', 'participants' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                // Format avatar URL properly for groups
                $avatarUrl = null;
                if ($conversation->avatar) {
                    $avatarUrl = asset('storage/' . $conversation->avatar);
                }

                return [
                    'id' => $conversation->id,
                    'name' => $conversation->name ?? 'Group Chat',
                    'type' => $conversation->type,
                    'community_id' => $conversation->community_id,
                    'avatar' => $avatarUrl,
                    'latest_message' => $conversation->latestMessage ? [
                        'message' => $conversation->latestMessage->message,
                        'created_at' => $conversation->latestMessage->created_at->diffForHumans(),
                        'user_name' => $conversation->latestMessage->user?->name ?? 'Unknown',
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
            'community_id' => 'nullable|exists:communities,id',
        ]);

        $conversation = DB::transaction(function () use ($request) {
            $creatorId = Auth::id();
            $conversation = ChatConversation::create([
                'name' => $request->name,
                'type' => 'group',
                'created_by' => $creatorId,
                'community_id' => $request->community_id ?? null,
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
            'community_id' => 'nullable|exists:communities,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userId = Auth::id();
        $userIds = $request->user_ids;
        
        // Add current user to the group if not already included
        if (!in_array($userId, $userIds)) {
            $userIds[] = $userId;
        }
        
        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('chat/avatars', 'public');
        }

        $conversation = DB::transaction(function () use ($request, $userIds, $userId, $avatarPath) {
            // Create group conversation
            $conversation = ChatConversation::create([
                'name' => $request->name,
                'type' => 'group',
                'created_by' => $userId,
                'community_id' => $request->community_id ?? null,
                'avatar' => $avatarPath,
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

        // Format avatar URL for response
        $conversationData = $conversation->toArray();
        if ($conversation->avatar) {
            $conversationData['avatar'] = asset('storage/' . $conversation->avatar);
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'conversation' => $conversationData,
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
            'messages' => $messages->items(),  // Get just the items array from paginated collection
            'conversation' => [
                'id' => $conversation->id,
                'name' => $conversation->name,
                'type' => $conversation->type,
                'community_id' => $conversation->community_id,
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

        // Check if this is a community conversation - verify user is a member
        if ($conversation->community_id) {
            // Check if user is a member
            $member = DB::table('community_members')
                ->where('community_id', $conversation->community_id)
                ->where('user_id', $userId)
                ->first();
            
            // User must be a member
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this community.'
                ], 403);
            }
        }

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

        // Handle @mentions - notify mentioned users
        $mentionedUsers = $message->getMentionedUsers();
        if (!empty($mentionedUsers)) {
            // Get mentioned user IDs
            if (in_array('everyone', $mentionedUsers)) {
                // Get all users in conversation
                $notifyUsers = $conversation->users()->where('user_id', '!=', $userId)->get();
            } else {
                // Get specific mentioned users
                $notifyUsers = User::whereIn('name', $mentionedUsers)
                    ->whereHas('chatParticipants', function ($query) use ($request) {
                        $query->where('conversation_id', $request->conversation_id);
                    })
                    ->where('id', '!=', $userId)
                    ->get();
            }
            
            // Create notifications for mentioned users
            foreach ($notifyUsers as $mentionedUser) {
                $mentionedUser->notify(new \App\Notifications\MentionedInChatNotification($message));
            }
        }

        // Broadcast event for real-time updates
        try {
            // If this is a community announcement, broadcast globally to all community members
            if ($conversation->community_id) {
                broadcast(new \App\Events\CommunityAnnouncementPosted($message, $conversation));
            }
            
            // Also broadcast to the conversation channel
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
        // Only get actual employees/users, exclude partners
        // Partners exist in separate partners table and should not appear in chat
        $users = User::where('id', '!=', Auth::id())
            ->excludePartners() // Exclude partner records
            ->select('id', 'name', 'email', 'avatar')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $avatarUrl = null;
                if ($user->avatar) {
                    if (Storage::disk('local')->exists($user->avatar)) {
                        $avatarUrl = Storage::disk('local')->url($user->avatar);
                    } else {
                        $avatarUrl = $user->avatar; // In case it's already a full URL
                    }
                }
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $avatarUrl,
                ];
            });

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
        try {
            $userId = Auth::id();

            $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->with(['users'])
                ->findOrFail($id);

            // Get creator information
            $creator = User::find($conversation->created_by);

            // Get all users in the conversation
            $users = $conversation->users()->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar ? asset($user->avatar) : null,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first() ?? 'Member',
                ];
            });

            return response()->json([
                'success' => true,
                'conversation' => [
                    'id' => $conversation->id,
                    'name' => $conversation->name,
                    'type' => $conversation->type,
                    'community_id' => $conversation->community_id,
                    'avatar' => $conversation->avatar ? asset('storage/' . $conversation->avatar) : null,
                    'created_by' => $conversation->created_by,
                    'creator' => $creator ? ['id' => $creator->id, 'name' => $creator->name] : null,
                    'created_at' => $conversation->created_at,
                ],
                'users' => $users,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Get conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conversation',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update conversation (e.g., change name)
     */
    public function updateConversation($id, Request $request)
    {
        try {
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
        } catch (\Throwable $e) {
            \Log::error('Update conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update conversation avatar
     */
    public function updateConversationAvatar($id, Request $request)
    {
        try {
            $userId = Auth::id();

            $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->findOrFail($id);

            // Only creator can update group conversations
            if ($conversation->type === 'group' && $conversation->created_by !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the group creator can update the group avatar.'
                ], 403);
            }

            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Delete old avatar if exists
            if ($conversation->avatar && \Storage::disk('public')->exists($conversation->avatar)) {
                \Storage::disk('public')->delete($conversation->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('chat/avatars', 'public');
            
            $conversation->update([
                'avatar' => $avatarPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar' => asset('storage/' . $avatarPath),
                'conversation' => $conversation
            ]);
        } catch (\Throwable $e) {
            \Log::error('Update conversation avatar error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete conversation
     */
    public function deleteConversation($id)
    {
        try {
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
        } catch (\Throwable $e) {
            \Log::error('Delete conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversation members
     */
    public function getConversationMembers($id)
    {
        try {
            $userId = Auth::id();

            $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->findOrFail($id);

            $members = User::whereIn('id', function ($query) use ($id) {
                $query->select('user_id')
                    ->from('chat_participants')
                    ->where('conversation_id', $id);
            })
                ->select('id', 'name', 'email', 'avatar')
                ->get();

            return response()->json([
                'success' => true,
                'members' => $members
            ]);
        } catch (\Throwable $e) {
            \Log::error('Get conversation members error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conversation members',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Add member to group conversation
     */
    public function addMember($id, Request $request)
    {
        try {
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
        } catch (\Throwable $e) {
            \Log::error('Add member error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add member',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * Also includes unread community announcements
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();

        // Get unread chat messages
        $totalUnread = ChatConversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->get()
            ->sum(function ($conversation) use ($userId) {
                return $conversation->unreadCount($userId);
            });

        // Get unread community announcements count
        $user = auth()->user();
        
        // Get communities that the user is part of
        $userCommunities = ChatConversation::where('type', 'group')
            ->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereNotNull('community_id')
            ->pluck('community_id')
            ->unique()
            ->toArray();

        // Also include communities the user created
        $createdCommunities = \App\Models\Community::where('created_by', $user->id)->pluck('id')->toArray();
        
        $communityIds = array_unique(array_merge($userCommunities, $createdCommunities));

        // Count active announcements
        $announcementCount = 0;
        if (!empty($communityIds)) {
            $announcementCount = \App\Models\CommunityAnnouncement::active()
                ->forBanner()
                ->whereIn('community_id', $communityIds)
                ->count();
        }

        return response()->json([
            'success' => true,
            'unread_count' => $totalUnread,
            'announcement_count' => $announcementCount,
            'total_count' => $totalUnread + $announcementCount,
        ]);
    }

    /**
     * Get users in a conversation (for mentions autocomplete)
     */
    public function getConversationUsers($conversationId)
    {
        try {
            $userId = Auth::id();

            $conversation = ChatConversation::whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->findOrFail($conversationId);

            $users = $conversation->users()->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'role' => $user->getRoleNames()->first() ?? 'Member',
                ];
            });

            return response()->json([
                'success' => true,
                'users' => $users,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Get conversation users error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conversation users',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get all communities for chat group creation
     */
    public function getCommunities()
    {
        try {
            $userId = Auth::id();
            
            // Only get communities where the user is a member
            $communities = \App\Models\Community::select('id', 'name', 'icon', 'color', 'avatar', 'created_by', 'posting_restricted')
                ->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'communities' => $communities,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Get communities error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch communities',
                'communities' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
