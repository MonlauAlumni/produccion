<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Inertia\Inertia;

class SocialController extends Controller
{
    public function show()
    {
        $popularGroups = Group::where('privacy', 'public')
            ->orderBy('members_count', 'desc')
            ->take(3)
            ->get();
        return Inertia::render('Social/Connect', [
            'groups' => Group::paginate(10)->items(),
            'popularGroups' => $popularGroups,
        ]);
        
    }
}
