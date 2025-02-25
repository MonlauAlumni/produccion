<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\Profile;

class CompanyObserver
{
    public function created(Company $company)
    {
        Profile::create([
            'company_id' => $company->id,
        ]);
    }
}

