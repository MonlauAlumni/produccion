<?php
namespace App\Http\Controllers\JobOffers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_offer_id' => 'required|exists:job_offers,id',
        ]);

        JobApplication::create([
            'job_offer_id' => $validated['job_offer_id'],
            'student_id' => auth()->user()->student->id,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Aplicación enviada correctamente');
    }
}