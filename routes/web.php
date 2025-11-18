<?php

use App\Livewire\ClientRegistrationForm;
use App\Livewire\CompaniesList;
use App\Livewire\CreateCompany;
use App\Livewire\EditCompany;
use App\Livewire\ExhibitorDetails;
use App\Livewire\ExhibitorsList;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public client registration routes (no auth required)
Route::get('register/{token}', ClientRegistrationForm::class)->name('client.register');
Route::get('registration/complete', function () {
    return view('client.complete');
})->name('client.complete');
Route::get('registration/expired', function () {
    return view('client.expired');
})->name('client.expired');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Company Management Routes
    Route::get('companies', CompaniesList::class)->name('companies.index');
    Route::get('companies/create', CreateCompany::class)->name('companies.create');
    Route::get('companies/{company}/edit', EditCompany::class)->name('companies.edit');
    Route::get('companies/{company}/submission', \App\Livewire\CompanySubmission::class)->name('companies.submission');

    // Exhibitor Management Routes
    Route::get('exhibitors', ExhibitorsList::class)->name('exhibitors.index');
    Route::get('exhibitors/{exhibitor}', ExhibitorDetails::class)->name('exhibitors.show');
});
