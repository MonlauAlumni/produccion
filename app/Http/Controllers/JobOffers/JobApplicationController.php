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
        
        // Get all applications for stats calculation
        $allApplications = JobApplication::where('job_offer_id', $jobOffers->id)
            ->with(['jobOffer.company', 'student.profile.education', 'student.profile.experience'])
            ->get();
          
            
        // Get filtered applications for display
        $applications = JobApplication::where('job_offer_id', $jobOffers->id)
            ->with(['jobOffer.company', 'student.profile.education', 'student.profile.experience']);
        
        if ($status) {
            $applications = $applications->where('status', $status);
        }
        
        $applications = $applications->get();
    } 
    else 
    {
        $jobOffers = JobOffer::where('company_id', Auth::user()->company->id)->get();
        
        // Get all applications for stats calculation
        $allApplications = JobApplication::whereIn('job_offer_id', $jobOffers->pluck('id'))
            ->with(['jobOffer.company', 'student.profile.education', 'student.profile.experience'])
            ->get();
          
        // Get filtered applications for display
        $applications = JobApplication::whereIn('job_offer_id', $jobOffers->pluck('id'))
            ->with(['jobOffer.company', 'student.profile.education', 'student.profile.experience']);
            
        if ($status) {
            $applications = $applications->where('status', $status);
        }
        
        $applications = $applications->get();
    }

    // Calculate stats from all applications regardless of filter
    $stats = [
        'pending' => $allApplications->where('status', 'pending')->count(),
        'in_process' => $allApplications->where('status', 'in_process')->count(),
        'interview' => $allApplications->where('status', 'interview')->count(),
        'accepted' => $allApplications->where('status', 'accepted')->count(),
        'rejected' => $allApplications->where('status', 'rejected')->count()
    ];
    
    return Inertia::render('Company/CandidateManagement/AllCandidates', [
        'stats' => $stats,
        'applications' => $applications,
        'jobOffers' => $jobOffers,
        'selectedStatus' => $request->status,
    ]);
}

    public function destroy($id)
    {
        $application = JobApplication::find($id);
        $application->delete();

        return redirect()->back()->with('success', 'Solicitud eliminada correctamente');
    }

    public function changeStatus(Request $request, $id)
    {
        $application = JobApplication::find($id);
        $application->status = $request->status;
        $application->save();

        return Inertia::location(route('job-applications.index'));

    }

    
}