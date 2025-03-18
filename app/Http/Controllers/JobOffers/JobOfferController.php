<?php

namespace App\Http\Controllers\JobOffers;

use App\Models\JobOffer;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class JobOfferController extends Controller
{
    
    
    public function index(Request $request)
    {
      
        $user = Auth::user();
        $page = $request->input('page', 1);
        $offers = JobOffer::with('company')->paginate(10);
            
        
        $offers->getCollection()->transform(function ($offer) use ($user) {
            $offer->isSaved = $user->savedJobOffers->contains($offer);
            
            return $offer;
        });

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
        dd("asada");
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
        
        // // Get saved job offers with pagination
        // $savedOffers = $user->savedJobOffers()
        //     ->with('company')
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(5)
        //     ->withQueryString();
        
        return Inertia::render('JobOffers/MyJobOffers', [
            'appliedOffers' => $appliedOffers,
           // 'savedOffers' => $savedOffers,
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