<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            
            // Allow all authenticated users for API requests (chat feature)
            if ($request->is('api/*')) {
                return $next($request);
            }
            
            // Restrict admin panel routes to Super Admin and CEO only
            if (!$user->hasRole(['Super Admin', 'CEO'])) {
                abort(403, 'Only Super Admin and CEO can manage communities.');
            }
            return $next($request);
        });
    }

    /**
     * Display all communities
     */
    public function index()
    {
        $communities = Community::with('creator')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.community.index', compact('communities'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.community.create');
    }

    /**
     * Store a new community
     */
    public function store(Request $request)
    {
        try {
            // Debug logging
            \Log::info('Community creation started', [
                'has_file' => $request->hasFile('avatar'),
                'files' => $request->allFiles(),
                'all_data' => $request->except(['avatar'])
            ]);
            
            // Parse member_ids if it's JSON string
            if ($request->has('member_ids') && is_string($request->member_ids)) {
                $request->merge([
                    'member_ids' => json_decode($request->member_ids, true)
                ]);
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:communities',
                'description' => 'nullable|string|max:1000',
                'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                'member_ids' => 'nullable|array',
                'member_ids.*' => 'exists:users,id',
                'posting_restricted' => 'nullable|boolean',
            ]);

            $validated['created_by'] = auth()->id();
            
            // Always use bullhorn icon for communities
            $validated['icon'] = 'bx-bullhorn';
            
            // Set default color if not provided
            if (!isset($validated['color']) || empty($validated['color'])) {
                $validated['color'] = '#667eea';
            }

            // Set posting_restricted, default to false
            $validated['posting_restricted'] = $validated['posting_restricted'] ?? false;

            $community = Community::create($validated);
            
            \Log::info('Community created', ['id' => $community->id, 'avatar' => $community->avatar]);
            
            // Automatically add creator as a member with posting permission
            DB::table('community_members')->insert([
                'community_id' => $community->id,
                'user_id' => auth()->id(),
                'added_by' => auth()->id(),
                'can_post' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add additional members if provided
            if (!empty($validated['member_ids'])) {
                $memberIds = $validated['member_ids'];
                
                // Filter out the creator if they're in the list
                $memberIds = array_filter($memberIds, function($id) {
                    return $id != auth()->id();
                });
                
                // Add each member
                foreach ($memberIds as $userId) {
                    // Check if already a member (shouldn't happen, but be safe)
                    $exists = DB::table('community_members')
                        ->where('community_id', $community->id)
                        ->where('user_id', $userId)
                        ->exists();
                        
                    if (!$exists) {
                        DB::table('community_members')->insert([
                            'community_id' => $community->id,
                            'user_id' => $userId,
                            'added_by' => auth()->id(),
                            'can_post' => true, // Default to true for new members
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Return JSON if API request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Community created successfully!',
                    'community' => $community,
                ], 201);
            }

            return redirect()->route('admin.communities.index')
                ->with('success', 'Community created successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Community validation error', ['errors' => $e->errors()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Community creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating community: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error creating community: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit(Community $community)
    {
        // UNRESTRICTED ACCESS: All authenticated users can edit communities
        // Load current members with their roles
        $members = $community->members()->with('roles')->get();
        
        // Get all users for the add member dropdown (excluding current members)
        $availableUsers = User::whereNotIn('id', $members->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.community.edit', compact('community', 'members', 'availableUsers'));
    }

    /**
     * Update a community
     */
    public function update(Request $request, Community $community)
    {
        // UNRESTRICTED ACCESS: All authenticated users can update communities
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:communities,name,' . $community->id,
            'description' => 'nullable|string|max:1000',
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'posting_restricted' => 'nullable|boolean',
        ]);
        
        // Always use bullhorn icon
        $validated['icon'] = 'bx-bullhorn';
        
        // Handle posting_restricted checkbox
        $validated['posting_restricted'] = $request->has('posting_restricted') ? (bool)$request->posting_restricted : false;
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($community->avatar && \Storage::disk('public')->exists($community->avatar)) {
                \Storage::disk('public')->delete($community->avatar);
            }
            $avatarPath = $request->file('avatar')->store('communities/avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $community->update($validated);

        return redirect()->route('admin.communities.index')
            ->with('success', 'Community updated successfully!');
    }

    /**
     * Delete a community
     */
    public function destroy(Community $community)
    {
        // UNRESTRICTED ACCESS: All authenticated users can delete communities
        // No permission check needed - auth middleware handles authentication

        // Note: community_announcements will cascade delete automatically via foreign key constraint
        // No need to manually detach - cascade handles cleanup
        
        $community->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Community deleted successfully!'
            ]);
        }

        return redirect()->route('admin.communities.index')
            ->with('success', 'Community deleted successfully!');
    }

    /**
     * Get communities for API (for announcement form dropdown)
     */
    public function getForAnnouncements()
    {
        $communities = Community::select('id', 'name', 'icon', 'color')
            ->orderBy('name')
            ->get();

        return response()->json($communities);
    }
    
    /**
     * Get community members
     */
    public function getMembers(Community $community)
    {
        $members = DB::table('community_members')
            ->join('users', 'community_members.user_id', '=', 'users.id')
            ->where('community_members.community_id', $community->id)
            ->select('users.id', 'users.name', 'users.email', 'users.avatar', 'community_members.can_post')
            ->get();
        
        return response()->json([
            'success' => true,
            'community' => $community,
            'members' => $members,
            'created_by' => $community->created_by
        ]);
    }
    
    /**
     * Add member to community
     */
    public function addMember(Request $request, Community $community)
    {
        // UNRESTRICTED ACCESS: All authenticated users can add members
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        // Verify the user is not a partner (partners should not be in communities)
        $user = User::excludePartners()->find($request->user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user. Partners cannot be added to communities.'
            ], 400);
        }
        
        // Check if already a member
        $exists = DB::table('community_members')
            ->where('community_id', $community->id)
            ->where('user_id', $request->user_id)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a member of this community.'
            ], 400);
        }
        
        DB::table('community_members')->insert([
            'community_id' => $community->id,
            'user_id' => $request->user_id,
            'added_by' => auth()->id(),
            'can_post' => true, // Default to true
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Member added successfully!'
        ]);
    }
    
    /**
     * Remove member from community
     */
    public function removeMember(Community $community, User $user)
    {
        // UNRESTRICTED ACCESS: All authenticated users can remove members
        // Note: Community creator can still be removed (no restriction)
        
        DB::table('community_members')
            ->where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Member removed successfully!'
        ]);
    }
    
    /**
     * Toggle member posting permission
     */
    public function toggleMemberPost(Community $community, User $user)
    {
        $member = DB::table('community_members')
            ->where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this community'
            ], 404);
        }
        
        $newCanPost = !$member->can_post;
        
        DB::table('community_members')
            ->where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->update([
                'can_post' => $newCanPost,
                'updated_at' => now()
            ]);
        
        return response()->json([
            'success' => true,
            'can_post' => $newCanPost,
            'message' => $newCanPost ? 'Member can now post announcements' : 'Member posting permission revoked'
        ]);
    }
    
}
