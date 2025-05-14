<?php

namespace App\Http\Controllers\JobOffers;

use App\Models\JobOffer;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Skill;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class JobOfferController extends Controller
{
    
    
    public function index(Request $request)
{
    $user = Auth::user();
    
    $categories = $request->input('categoria');
    $workModes = $request->input('trabajo'); // Puede ser string o array
    $minimumSalary = $request->input('minimum_salary');
    $maximumSalary = $request->input('maximum_salary');
    $searchQuery = $request->input('search'); // Añadimos el parámetro de búsqueda
    
    $offersQuery = JobOffer::with('company')
        ->whereIn('status', ['active', 'activo'])
        ->orderBy('created_at', 'desc');

    // Búsqueda por texto
    if ($searchQuery) {
        $offersQuery->where(function($query) use ($searchQuery) {
            $query->where('title', 'like', '%' . $searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $searchQuery . '%')
                  ->orWhereHas('company', function($q) use ($searchQuery) {
                      $q->where('title', 'like', '%' . $searchQuery . '%');
                  });
        });
    }

    // Filtrar por categorías (puede ser un array)
    if ($categories) {
        if (is_array($categories)) {
            // Si hay múltiples categorías, usamos whereIn o where con OR
            $offersQuery->where(function($query) use ($categories) {
                foreach ($categories as $category) {
                    $query->orWhere('category', 'like', '%' . $category . '%');
                }
            });
        } else {
            // Si es una sola categoría
            $offersQuery->where('category', 'like', '%' . $categories . '%');
        }
    }

    // Filtrar por modos de trabajo (puede ser un array)
    if ($workModes) {
        if (is_array($workModes)) {
            $offersQuery->whereIn('work_mode', $workModes);
        } else {
            $offersQuery->where('work_mode', $workModes);
        }
    }

    // Filtrar por rango salarial
    if ($minimumSalary && $maximumSalary) {
        $offersQuery->whereBetween('salary', [$minimumSalary, $maximumSalary]);
    }

    $offers = $offersQuery->paginate(10);

    // Marcar ofertas guardadas
    $offers->getCollection()->transform(function ($offer) use ($user) {
        $offer->isSaved = $user->savedJobOffers->contains($offer->id);
        return $offer;
    });

    return Inertia::render('Home', [
        'jobOffers' => $offers,
    ]);
}

    
    


    public function show($id)
    {
        $jobOffer = JobOffer::with(['company', 'skills', 'applicants'])->findOrFail($id);

     
        return Inertia::render('JobOffers/SingleJobOffer', [
            'jobOffer' => $jobOffer
        ]);
    }

    public function create()
    {

        $skills = Skill::all();
       
        return Inertia::render('JobOffers/CreateJobOffer', [
            'skills' => $skills,
        ]);
    }

    public function store(Request $request)
    {
    
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
            'contract_type' => $request->job_type,
        ]);
        // Sincronizar habilidades
    
        // Si se sube un archivo, manejarlo
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('job_offers', 'public');
            $jobOffer->file = $filePath;
            $jobOffer->save();
        }
         if ($request->filled('skills')) {
      $skillIds = collect($request->skills)->pluck('id')->toArray();
$jobOffer->skills()->sync($skillIds);
         }
    
        return redirect()->route('home');
    }


    public function myOffers(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('tab', 'applied');
        $status = $request->input('status');
        
        // Get applied job offers with pagination
        $appliedOffersQuery = JobApplication::with(['jobOffer', 'jobOffer.company'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
            
        // Filter by status if provided
        if ($status && $status !== 'all') {
            $appliedOffersQuery->where('status', $status);
        }
        
        $appliedOffers = $appliedOffersQuery->paginate(5)->withQueryString();
        
        // // // Get saved job offers with pagination
     $savedOffers = $user->savedJobOffers()
            ->with('company')
          ->orderBy('created_at', 'desc')
         ->paginate(5)
           ->withQueryString();
         
        
        return Inertia::render('JobOffers/MyJobOffers', [
            'appliedOffers' => $appliedOffers,
            'savedOffers' => $savedOffers,
            'activeTab' => $tab,
            'activeStatus' => $status ?? 'all'
        ]);
    }

    public function toggleSave(Request $request, $id)
    {
        $user = Auth::user();
        $jobOffer = JobOffer::findOrFail($id);
        
        if ($user->savedJobOffers->contains($jobOffer)) {
            $user->savedJobOffers()->detach($jobOffer);
            return redirect()->route('ofertas.index');
        }
        $user->savedJobOffers()->syncWithoutDetaching($jobOffer);
        
    
        return redirect()->route('ofertas.index');
    }

  

}