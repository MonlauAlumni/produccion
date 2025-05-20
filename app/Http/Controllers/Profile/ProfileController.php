<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Support\Facades\Storage;

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

        $authUser = Auth::user();

        $isSameUser = $authUser && $authUser->id === $user->id;
        $skillsByCategory = [];
        $areaMap = [
            'Informatica' => 'it',
            'Marketing' => 'marketing',
            'Automocion' => 'automotive',
        ];

        $mappedArea = $areaMap[$user->training_area] ?? null;

        // Obtener todas las skills según la categoría mapeada
        $allSkills = [];
        
        if ($mappedArea) {
            $allSkills = Skill::where('category', $mappedArea)->get(['id', 'name']);
        }
        

        return Inertia::render('Student/Profile', [
            'user' => $user,
            'profile' => $user->profile,
            'workExperiences' => $user->workExperiences,
            'educations' => $user->educations,
            'isSameUser' => $isSameUser,
            'slang' => $slang,
            'skills' => $user->profile->skills,
            'allSkills' => $allSkills,

        ]);
    }

    public function updateSkills(Request $request, $slang)
    {
        $request->validate([ 'skillId' => 'required|integer|exists:skills,id' ]);
        $user = User::whereHas('profile', fn($q) => $q->where('slang', $slang))->with('profile')->first();
         
        if (!$user) return abort(404);
        $profile = $user->profile;
        $profile->skills()->syncWithoutDetaching([$request->skillId]);
        $skill = Skill::find($request->skillId);
        return response()->json(['id' => $skill->id, 'name' => $skill->name]);
    }

    public function removeSkill(Request $request, $slang, $skillId)
    {
        $user = User::whereHas('profile', fn($q) => $q->where('slang', $slang))->with('profile')->first();
        if (!$user) return abort(404);
        $user->profile->skills()->detach($skillId);
        return response()->json(['removed' => $skillId]);
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
            $user = User::where('email', $githubUser['email'])->first();

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
            $user = User::where('email', $googleUser['email'])->first();

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

            $user = User::where('email', $microsoftUser['email'])->first();

        }
        Auth::login($user);
        $user->assignRole('alumne');
        $user->settings()->create();
        $user->profile()->create();
        $user->profile()->update(['profile_picture' => $microsoftUser['profile_photo_path'] ? $microsoftUser['profile_photo_path'] : null,]);
    
        $request->session()->regenerate();

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

        // Cambio aquí: verificar si hay un archivo 'cv' en la solicitud
        if ($request->hasFile('cv')) {

            if ($userProfile->cv_path) {
                Storage::disk('public')->delete($userProfile->cv_path);
            }

            $cvPath = $request->file('cv')->store('cv', 'public');

            $userProfile->cv_path = $cvPath;
        }

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

        return redirect()->back()->with('success', 'Perfil actualizado correctamente');
    }
    public function updateBanner(Request $request, $slang)
    {

        $user = User::with('profile')->get()->filter(function ($user) use ($slang) {
        return $user->profile && $user->profile->slang === $slang;
    })->first();

    // Verifica si llega el archivo
    $file = $request->file('banner_url');


    if (!$file) {
        return back()->withErrors(['banner_url' => 'No se recibió el archivo']);
    }

    $path = $file->store('banners', 'public');
    
    if (!$path) {
        return back()->withErrors(['banner_url' => 'Error al guardar el archivo']);
    }

    $user->profile->update(['banner_url' => '/storage/' . $path]);

      return Inertia::location(route('perfil.show', ['slang' => $user->profile->slang]))
        ;
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

    public function updateProfilePicture(Request $request, $slang)
    {
       
        $user = User::with('profile')->get()->filter(function ($user) use ($slang) {
            return $user->profile && $user->profile->slang === $slang;
        })->first();
    
        $path = '/storage/'.$request->file('profile_picture')->store('profile_picture', 'public');
       
        $user->profile->update(['profile_picture' => $path]);

        return Inertia::location(route('perfil.show', ['slang' => $user->profile->slang]));
    }

    public function downloadCV($slang)
    {
        // Buscar al usuario con el slang proporcionado
        $user = User::with('profile')->get()->filter(function ($user) use ($slang) {
            return $user->profile && $user->profile->slang === $slang;
        })->first();

        if (!$user) {
            abort(404, 'No existe el usuario');
        }

        $cvPath = $user->profile->cv_path;
        $filePath = storage_path('app/public/' . $cvPath);

        if (!file_exists($filePath)) {
            abort(404, 'No existe el archivo');
        }

        // Forzar la descarga del archivo
        return response()->download($filePath, basename($filePath), [
            'Content-Type' => 'application/pdf', // Especificamos que es un PDF
            'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"', // Esto asegura que se descargue
        ]);
    }




}



