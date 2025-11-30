<?php

use App\Livewire\VisitorRegistration;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('displays visitor registration page correctly', function () {
    $this->get('/visitor-register')
        ->assertSeeLivewire(VisitorRegistration::class)
        ->assertSee('Visitor Registration')
        ->assertSee('Personal Information')
        ->assertSee('Interests & Preferences');
});

it('validates required fields on step 1', function () {
    Livewire::test(VisitorRegistration::class)
        ->call('nextStep')
        ->assertHasErrors(['name', 'phone']);
});

it('can proceed to step 2 with valid step 1 data', function () {
    Livewire::test(VisitorRegistration::class)
        ->set('name', 'John Doe')
        ->set('phone', '9876543210')
        ->call('nextStep')
        ->assertSet('currentStep', 2);
});

it('validates required fields on step 2', function () {
    Livewire::test(VisitorRegistration::class)
        ->set('currentStep', 2)
        ->call('submit')
        ->assertHasErrors(['interests', 'planning_to_buy', 'areas']);
});

it('can successfully submit visitor registration', function () {
    Livewire::test(VisitorRegistration::class)
        ->set('name', 'John Doe')
        ->set('phone', '9876543210')
        ->set('email', 'john@example.com')
        ->set('company_name', 'Test Company')
        ->set('currentStep', 2)
        ->set('interests', ['Residential', 'Commercial'])
        ->set('residential_types', ['2 BHK', '3 BHK'])
        ->set('commercial_types', ['Shops'])
        ->set('planning_to_buy', 'Within 3 months')
        ->set('areas', ['Athwa - Vesu', 'Katargam'])
        ->call('submit')
        ->assertRedirect('/');

    expect(Visitor::where('name', 'John Doe')->exists())->toBeTrue();
});

it('can navigate between steps', function () {
    Livewire::test(VisitorRegistration::class)
        ->set('name', 'John Doe')
        ->set('phone', '9876543210')
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        ->call('previousStep')
        ->assertSet('currentStep', 1);
});
