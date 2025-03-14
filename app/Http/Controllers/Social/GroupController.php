<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupUser;
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
            ->with(['members.user', 'posts.user', 'posts.comments.user'])
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
}
