<?php
namespace App\Http\Controllers\JobOffers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
}