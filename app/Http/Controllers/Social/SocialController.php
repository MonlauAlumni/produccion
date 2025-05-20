<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Post;
use App\Models\Event;


class SocialController extends Controller
{
    public function show(Request $request)
    {
        $totalPosts = Post::whereNull('group_id')->count();
        $popularGroups = Group::where('privacy', 'public')
            ->orderBy('members_count', 'desc')
            ->take(3)
            ->get();

        $featuredStories = Post::with('user.profile')
            ->with('images')
            ->whereNull('group_id')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByRaw('(likes_count + comments_count) desc')
            ->take(2)
            ->get();

        $allContents = Post::whereNull('group_id')
            ->where('created_at', '>=', now()->subDays(7))
            ->pluck('content')
            ->map(fn($content) => strip_tags($content))
            ->implode(' ');

        $words = preg_split('/\s+/', mb_strtolower(preg_replace('/[^\p{L}\p{N}\s]+/u', '', $allContents)), -1, PREG_SPLIT_NO_EMPTY);
        $counts = array_count_values($words);
        arsort($counts);
        $trendingTopics = array_slice(
            array_map(
            fn($w, $c) => [
                'word' => $w,
                'count' => $c
            ],
            array_keys($counts),
            $counts
            ),
            0,
            5
        );


        $page = request()->input('page', 1);
        $postsPerPage = 5;
        $postsToLoad = $postsPerPage * $page;

        $has_more_posts = $postsToLoad < $totalPosts;

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


        $suggestedConnections = User::where('id', '!=', $request->user()->id)
        ->with(['profile', 'mutual_connections'])

            ->whereDoesntHave('connections', function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('user_id', $request->user()->id)
                    ->orWhere('connection_id', $request->user()->id);
                });
            })
            ->whereHas('connections', function($query) use ($request) {
                $query->whereIn('connection_id', function($subquery) use ($request) {
                    $subquery->select('connection_id')
                            ->from('connections')
                            ->where('user_id', $request->user()->id)
                            ->where('connections.status', 'accepted');
                })
                ->where('connections.status', 'accepted');
            })
            ->inRandomOrder()
            ->limit(5)
            ->get();
        
        
        
        $upcomingEvents = Event::with('photos')
            ->whereBetween('event_date', [now(), now()->addMonth()])
            ->where('is_private', false)
            ->orderBy('attendees_count', 'desc')
            ->take(3)
            ->get();

        $events = Event::with('photos')
            ->where('is_private', false)
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->get();
        
            $groups = Group::all();

        return Inertia::render('Social/Connect', [
            'groups' => $groups,
            'popularGroups' => $popularGroups,
            'posts' => $posts,
            'events' => $events,
            'upcomingEvents' => $upcomingEvents,
            'featuredStories' => $featuredStories,
            'trendingTopics' => $trendingTopics,
            'hasMorePosts' => $has_more_posts,
            'suggestedConnections' => $suggestedConnections
        ]);
        
    }

    public function showSearch(Request $request)
    {
        $query = $request->input('q', '');
        
        $results = [
            'users' => [],
            'groups' => [],
            'events' => [],
            'posts' => []
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
            
            $events = Event::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('location', 'like', "%{$query}%")
                ->with(['photos', 'attendees.user.profile'])
                ->where(function ($q) use ($id) { 
                    $q->where('is_private', false)
                        ->orWhereHas('group', function ($query) use ($id) {
                            $query->whereHas('members', function ($q) use ($id) {
                                $q->where('user_id', $id);
                            });
                        });
                })
                ->paginate(10)->items();  

            $posts = Post::where('content', 'like', "%{$query}%")
                ->whereNull('group_id')
                ->with(['user.profile', 'images', 'comments' => function ($query) {
                    $query->with('user.profile')->limit(3);
                }])
                ->where(function ($q) use ($id) {
                    $q->whereNull('group_id')
                      ->orWhereHas('group', function ($query) use ($id) {
                          $query->whereHas('members', function ($q) use ($id) {
                              $q->where('user_id', $id);
                          });
                      });
                })
                ->paginate(10)->items();
            
            $results['users'] = $users;
            $results['groups'] = $groups;
            $results['events'] = $events;
            $results['posts'] = $posts;
        }
        
        return Inertia::render('Social/Search', [
            'query' => $query,
            'results' => $results
        ]);
    }
}
