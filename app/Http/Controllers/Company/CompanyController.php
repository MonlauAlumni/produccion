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
        return Inertia::render('Company/SingleCompany', [
          'company' => $company,
            'isAdmin' => $isAdmin
        ]);
    }

    public function updateLogo(Request $request, $company)
    {
        $company = Company::where('slang', $company)->firstOrFail();
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $request->file('logo')->store('logos', 'public');
        $company->update(['profile_picture' => '/storage/' . $path]);   
        
        return redirect()->route('empresa.show', ['slang' => $company->slang]);
    }

}
