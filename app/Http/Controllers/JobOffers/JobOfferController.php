<?php

namespace App\Http\Controllers\JobOffers;

use App\Models\JobOffer;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class JobOfferController extends Controller
{
    
    
    public function index(Request $request)
    {
        // dd(JobOffer::all());

        $page = $request->input('page', 1);
        $offers = JobOffer::with('company')->paginate(10);
                
        return Inertia::render('Home', [
            'jobOffers' => $offers
        ]);
    }
    


    public function show($id)
    {
        $jobOffer = JobOffer::with('company')->findOrFail($id);

   
        return Inertia::render('JobOffers/SingleJobOffer', [
            'jobOffer' => $jobOffer
        ]);
    }

    public function create()
    {
        return Inertia::render('JobOffers/CreateJobOffer');
    }

    public function store(Request $request)
    {
        // Crear la oferta de trabajo
        $jobOffer = JobOffer::create([
            'company_id' => auth()->user()->company->id,
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'experience' => $request->experience,
            'responsibilities' => $request->responsibilities,
            'location' => $request->location,
            'benefits' => $request->benefits,
            'min_experience' => $request->min_experience,
            'min_studies' => $request->min_studies,
            'vacancies' => $request->vacancies,
            'minimum_salary' => $request->minimum_salary,
            'maximum_salary' => $request->maximum_salary,
            'work_mode' => $request->work_mode,
            'expires_at' => $request->expires_at,
            'deadline' => $request->deadline,
            'status' => 'active',
            'category' => $request->category,
        ]);
    
        // Si se sube un archivo, manejarlo
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('job_offers', 'public');
            $jobOffer->file = $filePath;
            $jobOffer->save();
        }
    
        return redirect()->route('home');
    }

  

}