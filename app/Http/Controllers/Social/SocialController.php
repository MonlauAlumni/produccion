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
        return Inertia::render('Social/Connect', [
            'groups' => Group::paginate(10)->items(),
        ]);
        
    }

}
