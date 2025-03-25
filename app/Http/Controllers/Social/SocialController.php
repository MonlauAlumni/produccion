<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Post;


class SocialController extends Controller
{
    public function show()
    {
        $popularGroups = Group::where('privacy', 'public')
            ->orderBy('members_count', 'desc')
            ->take(3)
            ->get();

        $page = request()->input('page', 1);
        $postsPerPage = 5;
        $postsToLoad = $postsPerPage * $page;
        $posts = Post::with('user.profile')
            ->with('images')
            ->with([
            'comments' => function ($query) {
                $query->with('user.profile')->limit(3);
            }
            ])
            ->whereNull('group_id')
            ->latest()
            ->take($postsToLoad)
            ->get();

        return Inertia::render('Social/Connect', [
            'groups' => Group::paginate(10)->items(),
            'popularGroups' => $popularGroups,
            'posts' => $posts,
        ]);
        
    }

    public function showSearch(Request $request)
    {
        $query = $request->input('q', '');
        
        $results = [
            'users' => [],
            'groups' => []
        ];

        $id = $request->user()->id;
        if ($query) {
            $users = User::where('id', '!=', $id) 
            ->where(function ($q) use ($query) {
                $q->whereRaw("CONCAT(name, ' ', last_name_1, ' ', last_name_2) like ?", ["%{$query}%"])
                    ->orWhereRaw("CONCAT(name, ' ', last_name_2, ' ', last_name_1) like ?", ["%{$query}%"])
                    ->orWhereRaw("CONCAT(last_name_1, ' ', name, ' ', last_name_2) like ?", ["%{$query}%"])
                    ->orWhereRaw("CONCAT(last_name_1, ' ', last_name_2, ' ', name) like ?", ["%{$query}%"])
                    ->orWhereRaw("CONCAT(last_name_2, ' ', name, ' ', last_name_1) like ?", ["%{$query}%"])
                    ->orWhereRaw("CONCAT(last_name_2, ' ', last_name_1, ' ', name) like ?", ["%{$query}%"])
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->whereHas('profile', function ($q) use ($query) {
                $q->where('slang', 'like', "%{$query}%")
                    ->orWhere('degree', 'like', "%{$query}%")
                    ->orWhere('job_title', 'like', "%{$query}%")
                    ->orWhere('location', 'like', "%{$query}%");
            })
            ->with('profile') 
            ->paginate(20) 
            ->items();
        
            $groups = Group::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->withCount('members')
                ->paginate(20)->items();
            
            $results['users'] = $users;
            $results['groups'] = $groups;
        }
        
        return Inertia::render('Social/Search', [
            'query' => $query,
            'results' => $results
        ]);
    }
}
