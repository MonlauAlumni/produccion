<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\GroupPost;
use App\Models\GroupPostComment;
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

        $group = new Group();
        $group->name = $request->name;
        $group->description = $request->description;
        $group->category = $request->category;
        $group->privacy = $request->privacy;
        $group->members_count = 1;
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

        return $this->show($group->slug);
    }

    public function show($slug)
    {
        $page = request()->input('page', 1);

        $group = Group::where('slug', $slug)
            ->with(['members.user.profile', 'events'])
            ->firstOrFail();
        
        $postsPerPage = 5;
        $postsToLoad = $postsPerPage * $page;

        $posts = $group->posts()
            ->with('user.profile')
            ->with('images') 
            ->with([
                'comments' => function ($query) {
                    $query->with('user.profile')->limit(3);
                }
            ])
            ->latest()
            ->take($postsToLoad)
            ->get();

        $totalPosts = $group->posts()->count();

        $group->posts = $posts;
        $group->total_posts = $totalPosts;
        $group->has_more_posts = $postsToLoad < $totalPosts;

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
            'currentPage' => (int)$page,
        ]);
    }

    public function updateLogo(Request $request, $slug)
    {
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $group = Group::where('slug', $slug)->firstOrFail();

        if ($request->hasFile('logo')) {
            if ($group->group_logo) {
                $oldLogoPath = str_replace('/storage/', '', $group->group_logo);
                Storage::disk('public')->delete($oldLogoPath);
            }

            $logoPath = $request->file('logo')->store('groups/logos', 'public');
            $group->group_logo = Storage::url($logoPath);
            $group->save();
        }

        return redirect()->back()->with('success', 'Logo actualizado con éxito!');
    }

    public function updateBanner(Request $request, $slug) {
        $request->validate([
            'banner' => 'required|image|max:2048',
        ]);

        $group = Group::where('slug', $slug)->firstOrFail();

        if ($request->hasFile('banner')) {
            if ($group->group_banner) {
                $oldBannerPath = str_replace('/storage/', '', $group->group_banner);
                Storage::disk('public')->delete($oldBannerPath);
            }

            $bannerPath = $request->file('banner')->store('groups/banners', 'public');
            $group->group_banner = Storage::url($bannerPath);
            $group->save();
        }

        return redirect()->back()->with('success', 'Banner actualizado con éxito!');
    }
   
    public function joinGroup(Request $request)
    {

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
