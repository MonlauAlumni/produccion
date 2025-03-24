<?php
namespace App\Http\Controllers\JobOffers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\JobOffer;

class JobApplicationController extends Controller
{
    public function store($jobOfferId)
    {
 
        $student = Auth::user();
   
        $existingApplication = JobApplication::where('job_offer_id', $jobOfferId)
            ->where('user_id', $student->id)
            ->first();
     
        if ($existingApplication) {
            return redirect()->back()->with('error', 'Ya has aplicado a esta oferta');
        }

        $jobOffer = JobOffer::find($jobOfferId);
       
        if ($jobOffer->deadline < now()) {
            return redirect()->back()->with('error', 'La oferta ha expirado');
        }
   


        JobApplication::create([
            'job_offer_id' => $jobOfferId,
            'user_id' => auth()->user()->id,
            'status' => 'pending',
        ]);

      
        return redirect()->route('home');
    }

    public function index(Request $request)
    {
        $status = $request->status;
        if ($request->job_offer)
        {
            $jobOffers = JobOffer::find($request->job_offer);
            $applications = JobApplication::where('job_offer_id', $jobOffers->id)
                ->with(['jobOffer.company', 'student.profile.education', 'student.profile.experience'])
                ->get();
        } else {
            $jobOffers = JobOffer::where('company_id', Auth::user()->company->id)->get();
            $applications = JobApplication::whereIn('job_offer_id', $jobOffers->pluck('id'))
                ->with(['jobOffer.company', 'student.profile.education', 'student.profile.experience'])
                ->get();
        }

        if ($status) {
            $applications = $applications->where('status', $status);
        }

    
        $stats = JobApplication::selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
        
  
           

        return Inertia::render('Company/CandidateManagement/AllCandidates', [
            'stats' => $stats,
            'applications' => $applications,
            'jobOffers' => $jobOffers,
        ]);
    }

    

    public function changeStatus(Request $request, $id)
    {
        $application = JobApplication::find($id);
        $application->status = $request->status;
        $application->save();

        return Inertia::location(route('job-applications.index'));

    }

    
}