<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use App\Models\Education;
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

        ]);
        $education->save();
    }
}
