<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityAnnouncement;
use App\Models\ChatConversation;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommunityAnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get active announcements for user's communities
     * GET /api/user/community-announcements
     */
    public function getUserCommunityAnnouncements(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Get communities that the user is part of (through chat conversations)
            $userCommunities = ChatConversation::where('type', 'group')
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereNotNull('community_id')
                ->pluck('community_id')
                ->unique()
                ->toArray();

            // Also include communities the user created
            $createdCommunities = Community::where('created_by', $user->id)->pluck('id')->toArray();
            
            $communityIds = array_unique(array_merge($userCommunities, $createdCommunities));

            // Return empty if no communities
            if (empty($communityIds)) {
                return response()->json([
                    'success' => true,
                    'announcements' => [],
                ]);
            }

            // Fetch active announcements
            $announcements = CommunityAnnouncement::active()
                ->forBanner()
                ->whereIn('community_id', $communityIds)
                ->with('community')
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($announcement) => [
                    'id' => $announcement->id,
                    'community_name' => $announcement->community ? $announcement->community->name : 'Unknown',
                    'community_id' => $announcement->community_id,
                    'title' => $announcement->title,
                    'message' => $announcement->message,
                    'priority' => $announcement->priority,
                    'priority_color' => $announcement->getPriorityColor(),
                    'priority_icon' => $announcement->getPriorityIcon(),
                    'created_at' => $announcement->created_at->format('M d, h:i A'),
                    'expires_at' => $announcement->expires_at?->format('M d, Y h:i A'),
                ]);

            return response()->json([
                'success' => true,
                'announcements' => $announcements,
            ]);
        } catch (\Exception $e) {
            \Log::error('Community Announcements Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading announcements',
                'announcements' => [],
            ]);
        }
    }

    /**
     * Get announcements for a specific community
     * GET /api/communities/:id/announcements
     */
    public function index(Community $community): JsonResponse
    {
        try {
            $user = auth()->user();
            
            \Log::info('Community Announcements Index Called', [
                'community_id' => $community->id,
                'user_id' => $user->id,
                'posting_restricted' => $community->posting_restricted,
            ]);
            
            // Check if user can post based on community settings
            $canPost = $this->userCanPost($community, $user);
            
            \Log::info('User Can Post Check', [
                'community_id' => $community->id,
                'user_id' => $user->id,
                'can_post' => $canPost,
            ]);
            
            // Get active announcements
            $announcements = CommunityAnnouncement::where('community_id', $community->id)
                ->where('is_active', 1)
                ->where(function($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->with('creator:id,name,avatar')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($announcement) {
                    return [
                        'id' => $announcement->id,
                        'title' => $announcement->title,
                        'message' => $announcement->message,
                        'priority' => $announcement->priority,
                        'created_by' => [
                            'id' => $announcement->creator->id,
                            'name' => $announcement->creator->name,
                            'avatar' => $announcement->creator->avatar,
                        ],
                        'created_at' => $announcement->created_at->toDateTimeString(),
                        'created_at_human' => $announcement->created_at->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'announcements' => $announcements,
                'can_post' => $canPost,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get community announcements error: ' . $e->getMessage());
            // On error, check basic permission
            return response()->json([
                'success' => true,
                'announcements' => [],
                'can_post' => !$community->posting_restricted,
                'message' => 'Error loading announcements, but you can still post',
            ]);
        }
    }

    /**
     * Check if user can post in community
     */
    private function userCanPost(Community $community, $user): bool
    {
        \Log::info('userCanPost Check Start', [
            'community_id' => $community->id,
            'user_id' => $user->id,
            'posting_restricted' => $community->posting_restricted,
            'created_by' => $community->created_by,
        ]);
        
        // Creator can always post
        if ((int)$community->created_by === (int)$user->id) {
            \Log::info('User is creator - allowing');
            return true;
        }

        // Check member record for explicit permission/restriction
        $memberRecord = \DB::table('community_members')
            ->where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->first();
        
        \Log::info('Member record check', [
            'member_record' => $memberRecord,
            'can_post' => $memberRecord ? $memberRecord->can_post : 'no record',
        ]);
        
        // If member record exists, respect the can_post setting
        if ($memberRecord) {
            // Explicit false = restricted
            if ($memberRecord->can_post === 0 || $memberRecord->can_post === false) {
                \Log::info('Member explicitly restricted - denying');
                return false;
            }
            // Explicit true or null (legacy) = allowed
            if ($memberRecord->can_post === 1 || $memberRecord->can_post === true || $memberRecord->can_post === null) {
                \Log::info('Member has permission - allowing');
                return true;
            }
        }
        
        // No member record: check if community posting is restricted
        if (!$community->posting_restricted) {
            \Log::info('No member record, posting not restricted - allowing');
            return true;
        }

        // Community restricted and user not in members - deny
        \Log::info('Permission denied - not a member of restricted community');
        return false;
    }

    /**
     * Process @mentions in announcement and send notifications
     */
    private function processMentions(CommunityAnnouncement $announcement, Community $community, $sender): void
    {
        try {
            $message = $announcement->message;
            $mentions = [];

            // Find @[Full Name] patterns (multi-word mentions)
            if (preg_match_all('/@\[([^\]]+)\]/', $message, $matches)) {
                $mentions = array_merge($mentions, $matches[1]);
            }

            // Find @word patterns (single-word mentions, including @everyone)
            $cleaned = preg_replace('/@\[[^\]]+\]/', '', $message);
            if (preg_match_all('/@(\w+)/', $cleaned, $matches)) {
                $mentions = array_merge($mentions, $matches[1]);
            }

            $mentions = array_unique($mentions);

            if (empty($mentions)) {
                return;
            }

            if (in_array('everyone', $mentions)) {
                // Notify all community members except sender
                $notifyUsers = $community->members()
                    ->where('user_id', '!=', $sender->id)
                    ->get();
            } else {
                // Notify specific mentioned users who are community members
                $notifyUsers = $community->members()
                    ->where('user_id', '!=', $sender->id)
                    ->where(function ($query) use ($mentions) {
                        foreach ($mentions as $name) {
                            $query->orWhere('name', 'LIKE', $name);
                        }
                    })
                    ->get();
            }

            foreach ($notifyUsers as $user) {
                \App\Models\Notification::createForUser(
                    $user->id,
                    'Mentioned in ' . $community->name,
                    ($sender->name ?? 'Someone') . ' mentioned you in an announcement: ' . substr($announcement->message, 0, 80) . (strlen($announcement->message) > 80 ? '...' : ''),
                    [
                        'type' => 'announcement_mention',
                        'icon' => 'bx bx-at',
                        'color' => 'warning',
                        'data' => [
                            'community_id' => $community->id,
                            'community_name' => $community->name,
                            'announcement_id' => $announcement->id,
                            'sender_name' => $sender->name ?? 'Unknown',
                            'sender_avatar' => $sender->avatar ?? null,
                        ],
                    ]
                );
            }
        } catch (\Exception $e) {
            \Log::error('Error processing announcement mentions: ' . $e->getMessage());
        }
    }

    /**
     * Create announcement for a community
     * POST /api/communities/:id/announcements
     */
    public function store(Request $request, Community $community): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Check if user has permission to post
            if (!$this->userCanPost($community, $user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to post announcements in this community',
                ], 403);
            }

            // Validation - simplified for chat-like posting
            $validated = $request->validate([
                'message' => 'required|string|max:5000',
                'title' => 'nullable|string|max:200',
                'priority' => 'nullable|in:info,normal,warning,urgent',
            ]);

            // Create announcement
            $announcement = CommunityAnnouncement::create([
                'community_id' => $community->id,
                'title' => $validated['title'] ?? null,
                'message' => $validated['message'],
                'priority' => $validated['priority'] ?? 'normal',
                'created_by' => $user->id,
                'show_in_banner' => true,
                'expires_at' => null,
                'is_active' => true,
            ]);

            // Load creator relationship
            $announcement->load('creator:id,name,avatar');

            // Broadcast to community channel - non-blocking
            try {
                broadcast(new \App\Events\CommunityAnnouncementPosted(
                    $announcement->toArray(),
                    $community->id,
                    $community->name
                ))->toOthers();
            } catch (\Exception $broadcastError) {
                // Log broadcast error but don't fail the request
                \Log::warning('Announcement broadcast failed: ' . $broadcastError->getMessage());
            }

            // Process @mentions in announcements
            $this->processMentions($announcement, $community, $user);

            return response()->json([
                'success' => true,
                'message' => 'Announcement posted successfully',
                'announcement' => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'message' => $announcement->message,
                    'priority' => $announcement->priority,
                    'created_by' => [
                        'id' => $announcement->creator->id,
                        'name' => $announcement->creator->name,
                        'avatar' => $announcement->creator->avatar,
                    ],
                    'created_at' => $announcement->created_at->toDateTimeString(),
                    'created_at_human' => $announcement->created_at->diffForHumans(),
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Create announcement error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create announcement',
            ], 500);
        }
    }

    /**
     * Update announcement
     * PUT /api/communities/:id/announcements/:announcementId
     */
    public function update(Request $request, Community $community, CommunityAnnouncement $announcement): JsonResponse
    {
        try {
            // Authorization check
            if ($announcement->community_id !== $community->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement does not belong to this community',
                ], 404);
            }

            $user = auth()->user();
            if ($announcement->created_by !== $user->id && !$user->hasRole([Roles::SUPER_ADMIN, Roles::MANAGER])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this announcement',
                ], 403);
            }

            // Validation
            $validated = $request->validate([
                'title' => 'nullable|string|max:200',
                'message' => 'required|string|max:5000',
                'priority' => 'nullable|in:info,normal,warning,urgent',
                'show_in_banner' => 'boolean',
                'expires_at' => 'nullable|date_format:Y-m-d H:i|after:now',
                'is_active' => 'boolean',
            ]);

            $announcement->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully',
                'announcement' => $announcement,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete announcement
     * DELETE /api/communities/:id/announcements/:announcementId
     */
    public function destroy(Community $community, CommunityAnnouncement $announcement): JsonResponse
    {
        try {
            // Authorization check
            if ($announcement->community_id !== $community->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement does not belong to this community',
                ], 404);
            }

            $user = auth()->user();
            if ($announcement->created_by !== $user->id && !$user->hasRole([Roles::SUPER_ADMIN, Roles::MANAGER])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this announcement',
                ], 403);
            }

            $announcement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Poll for new announcements since a given timestamp
     * GET /api/chat/announcements/poll?since=2026-02-09T10:00:00
     */
    public function poll(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $since = $request->query('since');

            // Get user's community IDs
            $communityIds = \DB::table('community_members')
                ->where('user_id', $user->id)
                ->pluck('community_id')
                ->toArray();

            // Also include communities the user created
            $createdIds = Community::where('created_by', $user->id)->pluck('id')->toArray();
            $communityIds = array_unique(array_merge($communityIds, $createdIds));

            if (empty($communityIds)) {
                return response()->json(['success' => true, 'announcements' => []]);
            }

            // Fetch NEW announcements (created after $since)
            // AND EDITED announcements (updated after $since where updated != created)
            $baseQuery = CommunityAnnouncement::whereIn('community_id', $communityIds)
                ->with('community:id,name,color')
                ->where('created_by', '!=', $user->id)
                ->limit(5);

            if ($since) {
                // Get announcements that are either new OR recently edited
                $baseQuery->where(function ($q) use ($since) {
                    $q->where('created_at', '>', $since)
                      ->orWhere(function ($q2) use ($since) {
                          $q2->where('updated_at', '>', $since)
                             ->whereColumn('updated_at', '!=', 'created_at');
                      });
                });
            } else {
                // First load: only get announcements from last 2 minutes
                $baseQuery->where(function ($q) {
                    $q->where('created_at', '>=', now()->subMinutes(2))
                      ->orWhere('updated_at', '>=', now()->subMinutes(2));
                });
            }

            $announcements = $baseQuery->orderBy('updated_at', 'desc')->get()->map(fn($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'message' => $a->message,
                'priority' => $a->priority ?? 'normal',
                'community_id' => $a->community_id,
                'community_name' => $a->community?->name ?? 'Community',
                'community_color' => $a->community?->color ?? '#667eea',
                'created_at' => $a->created_at->toIso8601String(),
                'updated_at' => $a->updated_at->toIso8601String(),
            ]);

            return response()->json([
                'success' => true,
                'announcements' => $announcements,
                'server_time' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Announcement poll error: ' . $e->getMessage());
            return response()->json(['success' => false, 'announcements' => []], 500);
        }
    }
}
