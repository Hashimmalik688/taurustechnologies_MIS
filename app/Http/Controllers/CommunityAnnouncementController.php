<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityAnnouncement;
use App\Models\ChatConversation;
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
        
        // If posting is not restricted, everyone can post
        if (!$community->posting_restricted) {
            \Log::info('Posting not restricted - allowing');
            return true;
        }

        // Creator can always post
        if ((int)$community->created_by === (int)$user->id) {
            \Log::info('User is creator - allowing');
            return true;
        }

        // Check if user has explicit permission via community_members
        $memberRecord = \DB::table('community_members')
            ->where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->first();
        
        \Log::info('Member record check', [
            'member_record' => $memberRecord,
            'can_post' => $memberRecord ? $memberRecord->can_post : 'no record',
        ]);
        
        // Member must exist and have can_post = true (or null for backwards compatibility)
        if ($memberRecord && ($memberRecord->can_post === null || (bool)$memberRecord->can_post)) {
            \Log::info('Member has permission - allowing');
            return true;
        }

        // If posting is restricted and user is not a member with permission
        \Log::info('Permission denied');
        return false;
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

            // Broadcast to community channel
            broadcast(new \App\Events\CommunityAnnouncementPosted(
                $announcement->toArray(),
                $community->id,
                $community->name
            ))->toOthers();

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
            if ($announcement->created_by !== $user->id && !$user->hasRole(['Super Admin', 'Manager'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this announcement',
                ], 403);
            }

            // Validation
            $validated = $request->validate([
                'title' => 'required|string|max:200',
                'message' => 'required|string|max:5000',
                'priority' => 'required|in:info,warning,urgent',
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
            if ($announcement->created_by !== $user->id && !$user->hasRole(['Super Admin', 'Manager'])) {
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
}
