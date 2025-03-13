<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Profile;

use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{

    public function profile($slang)
{   

    $user = User::whereHas('profile', function ($query) use ($slang) {
        $query->where('slang', $slang);
    })->with('profile')->first();

    if (!$user) {
        return Inertia::render('404_page');
     }

    $authUserId = auth()->user()->id;
    $user = User::with('profile')->find($authUserId);


    
  
    
    $authUser = Auth::user();
   
    $isSameUser = $authUser && $authUser->id === $user->id;
    
    return Inertia::render('Student/Profile', [
        'user' => $user,
        'profile' => $user->profile,
        'workExperiences' => $user->workExperiences,
        'educations' => $user->educations,
        'isSameUser' => $isSameUser,
        'slang' => $slang,
      
        
    ]);
}

    public function show()
    {

        $googleUser = Session::get('google_user');
        $githubUser = Session::get('github_user');
        $microsoftUser = Session::get('microsoft_user');


        $user = $googleUser ?? $githubUser ?? $microsoftUser;



        if ($user) {
            return inertia('Student/CompleteProfile', [
                'email' => $user['email'],
            ]);
        }
    }


    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'training_area' => 'required|in:Informatica,Marketing,Automocion',
        ]);


        $githubUser = Session::get('github_user');
        $googleUser = Session::get('google_user');
        $microsoftUser = Session::get('microsoft_user');

        if ($githubUser) {
            $user = User::create([
                'email' => $githubUser['email'],
                'github_id' => $githubUser['github_id'],
                'google_id' => null,
                'password' => $githubUser['password'],

                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
        } elseif ($googleUser) {
            $user = User::create([
                'email' => $googleUser['email'],
                'google_id' => $googleUser['google_id'],
                'password' => $googleUser['password'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
        } elseif ($microsoftUser) {
            $user = User::create([
                'email' => $microsoftUser['email'],
                'microsoft_id' => $microsoftUser['microsoft_id'],
                'password' => $microsoftUser['password'],
                'name' => $request->input('name'),
                'last_name_1' => $request->input('last_name_1'),
                'last_name_2' => $request->input('last_name_2'),
                'training_area' => $request->input('training_area'),
            ]);
        }
        $user->assignRole('alumne');

        // Limpiar los datos de la sesión
        Session::forget('github_user');
        Session::forget('google_user');
        Session::forget('microsoft_user');

        return redirect('/home')->with('success', 'Perfil completado con éxito. ¡Por favor, inicia sesión!');
    }



    public function update(Request $request, $slang)
    {
  
        $user = User::with('profile')->get()->filter(function ($user) use ($slang) {
            return $user->profile && $user->profile->slang === $slang;
        })->first();

        $userProfile = $user->profile;



        $user->update([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2,
            'email' => $request->email,
        ]);

        $userProfile->update([
            'description' => $request->description,
            'location' => $request->location,
            'phone' => $request->phone,
            'availability' => $request->availability,
            'degree' => $request->degree,
            'job_title' => $request->job_title,
            'graduation_year' => $request->graduation_year,
            'linkedin' => $request->linkedin,
            'github' => $request->github,
            'website' => $request->website,
        ]);

    }
        public function updateBanner(Request $request, $slang)
    {
        $user = User::with('profile')->get()->filter(function ($user) use ($slang) {
            return $user->profile && $user->profile->slang === $slang;
        })->first();

        $userProfile = $user->profile;
  
        $userProfile->update([
            'banner_url' => $request->banner_url,
        ]);
    }

    public function updateCV(Request $request, $slang)
    {
        $user = User::with('profile')->get()->filter(function ($user) use ($slang) {
            return $user->profile && $user->profile->slang === $slang;
        })->first();

        $userProfile = $user->profile;

        $userProfile->update([
            'cv_path' => $request->cv_path,
        ]);
    }

    public function updateLogo(Request $request, $slang)
    {
        $user = User::with('profile')->get()->filter(function ($user) use ($slang) {
            return $user->profile && $user->profile->slang === $slang;
        })->first();

        $userProfile = $user->profile;

        $userProfile->update([
            'profile_picture' => $request->profile_picture,
        ]);
    }
}



