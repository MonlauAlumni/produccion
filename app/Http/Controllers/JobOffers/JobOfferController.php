<?php

namespace App\Http\Controllers\JobOffers;

use App\Models\JobOffer;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        // Obtener las ofertas paginadas (aunque no se usa en este caso)
        $offers = JobOffer::with('company')->paginate(10);
    
        // Pasar el string 'hola' a Inertia
        return Inertia::render('Home', [
            'jobOffers' =>  $offers
        ]);
    }
    


    public function show(JobOffer $jobOffer)
    {
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
        'company_id' => $request->user()->company->id,
        'title' => $request->title,
        'description' => $request->description,
        'minExperience' => $request->minExperience,
        'minStudies' => $request->minStudies,
        'vacancies' => $request->vacancies,
        'workMode' => $request->workMode,
        'salary' => $request->salary,
        'salaryAmount' => $request->salaryAmount,
        'expires_at' => $request->expires_at, // Si tienes un campo para fecha de expiración
        'status' => 'active', // Si tienes un campo de estado para la oferta
        'category' => 'it', // Si hay una categoría para la oferta
    ]);

    // Si se pasan habilidades, asociarlas con la oferta
    if ($request->has('skills') && count($request->skills) > 0) {
        $jobOffer->skills()->sync($request->skills); // Usa sync para añadir habilidades
    }

    // Si se sube un archivo, manejarlo
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('job_offers', 'public');
        $jobOffer->file = $filePath;
        $jobOffer->save();
    }

        return redirect()->route('home');
    }

    

}