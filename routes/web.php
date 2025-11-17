<?php

use App\Livewire\CompaniesList;
use App\Livewire\CreateCompany;
use App\Livewire\DraftExhibitorsList;
use App\Livewire\EditCompany;
use App\Livewire\ExhibitorForm;
use App\Livewire\ExhibitorsList;
use App\Livewire\PublicExhibitorForm;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public exhibitor registration routes
Route::get('register-exhibitor', PublicExhibitorForm::class)->name('exhibitor.public.register');
Route::get('exhibitor/thank-you', function () {
    return view('exhibitor.thank-you');
})->name('exhibitor.public.thank-you');

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

    Route::get('exhibitor/register', ExhibitorForm::class)->name('exhibitor.register');
    Route::get('exhibitors', ExhibitorsList::class)->name('exhibitors.index');
    Route::get('exhibitors/drafts', DraftExhibitorsList::class)->name('exhibitors.drafts');

    // Admin: Company Management Routes
    Route::get('companies', CompaniesList::class)->name('companies.index');
    Route::get('companies/create', CreateCompany::class)->name('companies.create');
    Route::get('companies/{company}/edit', EditCompany::class)->name('companies.edit');
});
