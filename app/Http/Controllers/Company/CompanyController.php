<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Company;

class CompanyController extends Controller
{
    public function show($slang){
        
        $company = Company::where('slang', $slang)->firstOrFail();
        if (!$company){
            abort(404);
        }
        $userId = auth()->id();
        $isAdmin = ($userId === $company->user_id);
        //dd($company);
        return Inertia::render('Company/SingleCompany', [
            'company' => $company,
            'isAdmin' => $isAdmin
        ]);
    }

    public function updateLogo(Request $request, $company)
    {
        $company = Company::where('slang', $company)->firstOrFail();
 
       

        $path = $request->file('logo')->store('logos', 'public');
        $company->update(['profile_picture' => '/storage/' . $path]);   

        return redirect()->route('empresa.show', ['slang' => $company->slang]);
    }

    public function updateBanner(Request $request, $company)
    {
        $company = Company::where('slang', $company)->firstOrFail();
 
        $path = $request->file('banner_url')->store('banners', 'public');
        $company->update(['banner_url' => '/storage/' . $path]);   

        return redirect()->route('empresa.show', ['slang' => $company->slang]);
    }   

    public function update(Request $request, $slang)
    {
   
        $company = Company::where('slang', $slang)->firstOrFail();

        $company->update([
            'company_name' => $request->company_name,
            'company_phone' => $request->company_phone,
            'description' => $request->description,
            'sector' => $request->sector,
            'size' => $request->size,
            'location' => $request->location,
            'linkedin' => $request->linkedin,
            'twitter' => $request->twitter,
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'website' => $request->website,
            'founded_year' => $request->founded_year,
            'email' => $request->email,

        ]);
    
        return redirect()->route('empresa.show', ['slang' => $company->slang]);
    }

}
