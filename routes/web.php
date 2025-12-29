<?php

use App\Livewire\ClientRegistrationForm;
use App\Livewire\CompaniesList;
use App\Livewire\CreateCompany;
use App\Livewire\EditCompany;
use App\Livewire\PublicExhibitorsList;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\VisitorRegistration;
use App\Livewire\VisitorsList;
use App\Models\Company;
use App\Models\Exhibitor;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    // Get main sponsors (Powered By Sponsor 01, 02, 03) - show companies directly
    $mainSponsorCompanies = Company::with(['exhibitor', 'exhibitor.projects'])
        ->where('stall_type', 'ILIKE', 'Powered By Sponsor%')
        ->where('category', 'Builders')
        ->orderBy('stall_number', 'asc')
        ->limit(3)
        ->get();

    // Get other exhibitors (excluding main sponsors)
    $exhibitors = Exhibitor::with(['company', 'projects'])
        ->whereHas('company', function ($query) {
            $query->where('category', 'Builders')
                ->where(function ($q) {
                    $q->where('stall_type', 'NOT ILIKE', 'Powered By Sponsor%')
                        ->orWhereNull('stall_type');
                });
        })
        ->join('companies', 'exhibitors.company_id', '=', 'companies.id')
        ->orderBy('companies.company_name', 'asc')
        ->select('exhibitors.*')
        ->get();

    return view('welcome', [
        'mainSponsors' => $mainSponsorCompanies,
        'exhibitors' => $exhibitors,
    ]);

    return view('welcome', [
        'mainSponsors' => $mainSponsors,
        'exhibitors' => $exhibitors,
    ]);
})->name('home');

// Public exhibitors listing page
Route::get('/explore-exhibitors', PublicExhibitorsList::class)->name('public.exhibitors');

// Exhibitor Portal Routes (must be before /exhibitor/{exhibitor} wildcard route)
Route::prefix('exhibitor')->name('exhibitor.')->group(function () {
    Route::middleware(['guest:exhibitor'])->group(function () {
        Route::get('login', \App\Livewire\Exhibitor\Login::class)->name('login');
    });

    Route::middleware(['exhibitor.auth'])->group(function () {
        Route::get('dashboard', \App\Livewire\Exhibitor\Dashboard::class)->name('dashboard');
        Route::get('scan', \App\Livewire\Exhibitor\ScanVisitor::class)->name('scan');
        Route::get('leads', \App\Livewire\Exhibitor\Leads::class)->name('leads');

        Route::post('logout', function () {
            auth('exhibitor')->logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // Use a full page redirect instead of Livewire navigate
            return redirect(route('exhibitor.login'));
        })->name('logout');
    });
});

Route::get('/exhibitor/{exhibitor}', function (Exhibitor $exhibitor) {
    \App\Services\Analytics::trackExhibitorView($exhibitor);

    return view('exhibitor.show', [
        'exhibitor' => $exhibitor->load(['company', 'projects']),
    ]);
})->name('exhibitor.show');

Route::get('/project/{project}', function (Project $project) {
    \App\Services\Analytics::trackProjectView($project);

    return view('project.show', [
        'project' => $project->load(['exhibitor']),
    ]);
})->name('project.show');

// Analytics tracking routes
Route::get('/track/exhibitor/{exhibitor}/brochure', [App\Http\Controllers\AnalyticsController::class, 'downloadExhibitorBrochure'])->name('track.exhibitor.brochure');
Route::get('/track/project/{project}/brochure', [App\Http\Controllers\AnalyticsController::class, 'downloadProjectBrochure'])->name('track.project.brochure');
Route::get('/track/exhibitor/{exhibitor}/call', [App\Http\Controllers\AnalyticsController::class, 'trackCallExhibitor'])->name('track.exhibitor.call');
Route::get('/track/project/{project}/call', [App\Http\Controllers\AnalyticsController::class, 'trackCallProject'])->name('track.project.call');
Route::get('/track/exhibitor/{exhibitor}/website', [App\Http\Controllers\AnalyticsController::class, 'trackWebsiteVisit'])->name('track.exhibitor.website');

// Public client registration routes (no auth required)
Route::get('register/{token}', ClientRegistrationForm::class)->name('client.register');
Route::get('registration/complete', function () {
    return view('client.complete');
})->name('client.complete');
Route::get('registration/expired', function () {
    return view('client.expired');
})->name('client.expired');

// Public visitor registration routes (no auth required)
Route::get('visitor-register', VisitorRegistration::class)->name('visitor.register');
Route::get('visitor-success/{visitor}', \App\Livewire\VisitorSuccess::class)->name('visitor.success');
Route::get('visitors/{visitor}', \App\Livewire\VisitorShow::class)->name('visitor.show');

// Exhibitor QR validation route
Route::get('exhibitor/validate/{uuid}', [\App\Http\Controllers\ExhibitorValidationController::class, 'validate'])->name('exhibitor.validate');

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
    Route::get('companies/{company}/analytics', \App\Livewire\CompanyAnalytics::class)->name('companies.analytics');

    // Visitor Management Routes
    Route::get('visitors', VisitorsList::class)->name('visitors.index');

    // Admin Routes (restricted to viraj@glam2026.com)
    Route::middleware('can:admin-access')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', \App\Livewire\Admin\Users::class)->name('users');
        Route::get('activity-logs', \App\Livewire\Admin\ActivityLogs::class)->name('activity-logs');
    });
});
