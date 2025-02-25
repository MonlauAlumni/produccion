<?php

namespace App\Http\Controllers\JobOffers;

use App\Models\JobOffer;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class JobOfferController extends Controller
{
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

}