<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use App\Models\Education;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
{
    public function storeWork(Request $request)
    {
        $experience = WorkExperience::create([
            'user_id' => Auth::id(),
            'company_name' => $request->company_name,
            'position' => $request->position,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'location' => $request->location,
            'current' => $request->current ?? false,
        ]);

       
    }

    public function storeEducation(Request $request)
    {
        $education = Education::create([
            'user_id' => Auth::id(),
            'degree' => $request->degree,
            'institution' => $request->institution,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'location' => $request->location,
            'current' => $request->current ?? false,
        ]);

        
    }
    
    // Nuevos métodos para actualizar
    public function updateWork(Request $request, $slang, $id)
    {
        $experience = WorkExperience::findOrFail($id);
        
        // Verificar que el usuario sea el propietario
        if ($experience->user_id !== Auth::id()) {
            return Inertia::render('404_page');
        }
        
        $experience->update([
            'company_name' => $request->company_name,
            'position' => $request->position,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'location' => $request->location,
            'current' => $request->current ?? false,
        ]);
        
       
    }
    
    public function updateEducation(Request $request, $slang, $id)
    {
        $education = Education::findOrFail($id);
        
        // Verificar que el usuario sea el propietario
        if ($education->user_id !== Auth::id()) {
           return Inertia::render('404_page');  
        }
        
        $education->update([
            'degree' => $request->degree,
            'institution' => $request->institution,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'location' => $request->location,
            'current' => $request->current ?? false,
        ]);
        
      
    }
    
    // Nuevos métodos para eliminar
    public function deleteWork($slang, $id)
    {
        $experience = WorkExperience::findOrFail($id);
        
        // Verificar que el usuario sea el propietario
        if ($experience->user_id !== Auth::id()) {
            return Inertia::render('404_page');
        }
        
        $experience->delete();
        
        
    }
    
    public function deleteEducation($slang, $id)
    {
        $education = Education::findOrFail($id);
        
        // Verificar que el usuario sea el propietario
        if ($education->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta formación'
            ], 403);
        }
        
        $education->delete();
        
        
    }
}

