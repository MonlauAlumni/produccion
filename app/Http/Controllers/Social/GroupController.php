<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\GroupPost;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{

    public function index()
    {
        $groups = Group::with('members')->get();
        
        return Inertia::render('Social/Groups', [
            'groups' => $groups
        ]);
    }

    public function showCreateGroup()
    {
        return Inertia::render('Social/CreateGroup');
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string',
            'privacy' => 'required|in:public,private',
            'tags' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ]);
        
        $slug = Str::slug($request->name);
        $uniqueSlug = $slug;
        $counter = 1;
        
        while (Group::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }
        
        // Crear el grupo
        $group = new Group();
        $group->name = $request->name;
        $group->description = $request->description;
        $group->category = $request->category;
        $group->privacy = $request->privacy;
        $group->slug = $uniqueSlug;
        $group->creator_id = $request->user()->id;
        
        if ($request->has('tags')) {
            $tags = json_decode($request->tags);
            $group->tags = $tags;
        }
        
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('groups/logos', 'public');
            $group->group_logo = Storage::url($logoPath);
        }
        
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('groups/banners', 'public');
            $group->group_banner = Storage::url($bannerPath);
        }
        
        $group->save();
        
        GroupUser::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'admin',
            'joined_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return Inertia::render('Social/SingleGroup', [
            'group' => $group->load('members.user'),
            'isAdmin' => true,
        ]);
    }

    public function show($slug)
    {
        $group = Group::where('slug', $slug)
            ->with(['members.user.profile', 'posts.user.profile', 'posts.comments.user.profile'])
            ->firstOrFail();
        
        $isAdmin = false;
        $isMember = false;
        
        if (Auth::check()) {
            $member = $group->members()->where('user_id', Auth::id())->first();
            
            if ($member) {
                $isAdmin = $member->role === 'admin';
                $isMember = true;
            }
        }
        
        return Inertia::render('Social/SingleGroup', [
            'group' => $group,
            'isAdmin' => $isAdmin,
            'isMember' => $isMember,
        ]);
    }

    public function storePost(Request $request, $groupId)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('groups/group_posts', 'public');
        }

        $sanitizedContent = Purifier::clean($request->content);

        $post = GroupPost::create([
            'group_id' => $groupId,
            'user_id' => Auth::id(),
            'content' => $sanitizedContent,
            'image' => '/storage/'.$imagePath,
        ]);

        return redirect()->back()->with('success', 'Post creado con éxito!');
    }

    public function joinGroup(Request $request) {

        $group = Group::where('slug', $request->slug)->firstOrFail();
        if ($group->privacy === 'private') {
            return redirect()->back()->with('error', 'Este grupo es privado, no puedes unirte sin invitación.');
        }

        $group->members_count++;
        $group->save();

        GroupUser::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'member',
            'joined_at' => now(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Te has unido al grupo correctamente!');

    }

}
