<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\User;    
use App\Models\Company;

class CompanyController extends Controller
{
    public function show($slang){
        
        $company = Company::where('slang', $slang)->with('jobOffers')->firstOrFail();

        if (!$company) {
            return Inertia::render('404_page');
         }

        $userId = auth()->id();
        $isAdmin = ($userId === $company->user_id);
        
        $company->jobOffers = $company->jobOffers->toArray();

        
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

    public function index(Request $request)
{
    $search = $request->input('search', '');
    $sector = $request->input('sector', '');
    $location = $request->input('location', '');
  
    // Obtener empresas con filtros
    $companies = Company::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        })
        ->when($sector, function ($query, $sector) {
            return $query->where('sector', $sector);
        })
        ->when($location, function ($query, $location) {
            return $query->where('location', 'like', "%{$location}%");
        })
        ->paginate(12)
        ->withQueryString();
  
    // Obtener ubicaciones populares para el filtro
    $locations = Company::select('location')
        ->distinct()
        ->whereNotNull('location')
        ->orderBy('location')
        ->limit(15)
        ->get()
        ->pluck('location');
  
    return Inertia::render('Company/Index', [
        'companies' => $companies,
        'filters' => [
            'search' => $search,
            'sector' => $sector,
            'location' => $location,
        ],
        'locations' => $locations,
    ]);
}

}
