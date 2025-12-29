<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;

class ExhibitorValidationController extends Controller
{
    public function validate(string $uuid): RedirectResponse
    {
        $company = Company::where('uuid', $uuid)->first();

        if (! $company) {
            return redirect()->route('home')->with('error', 'Company not found. Please contact the organizer.');
        }

        return redirect()->away($company->whatsapp_inquiry_url);
    }
}
