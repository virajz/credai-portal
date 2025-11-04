<?php

use App\Livewire\PublicExhibitorForm;
use App\Models\DraftExhibitor;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

test('draft is created when user starts filling form', function () {
    $component = Livewire::test(PublicExhibitorForm::class);

    // No draft should exist on mount
    expect(DraftExhibitor::count())->toBe(0);

    // Draft should be created when user starts typing
    $component->set('brand_name', 'Test Company');

    expect(DraftExhibitor::count())->toBe(1);
    expect(DraftExhibitor::first()->resume_token)->not->toBeEmpty();
    expect(DraftExhibitor::first()->brand_name)->toBe('Test Company');
});

test('draft is auto-saved when form data changes', function () {
    $component = Livewire::test(PublicExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', '123 Test St')
        ->set('city', 'Surat');

    assertDatabaseHas('draft_exhibitors', [
        'brand_name' => 'Test Company',
        'office_address' => '123 Test St',
        'city' => 'Surat',
    ]);
});

test('draft is loaded when resume token is provided', function () {
    $draft = DraftExhibitor::create([
        'resume_token' => DraftExhibitor::generateResumeToken(),
        'brand_name' => 'Existing Company',
        'office_address' => '456 Old St',
        'city' => 'Navsari',
        'contact_person_name' => 'Jane Doe',
        'phone_number' => '9876543210',
        'current_step' => 2,
        'completed_steps' => [1],
        'last_activity_at' => now(),
    ]);

    Livewire::withQueryParams(['resume' => $draft->resume_token])
        ->test(PublicExhibitorForm::class)
        ->assertSet('brand_name', 'Existing Company')
        ->assertSet('office_address', '456 Old St')
        ->assertSet('city', 'Navsari')
        ->assertSet('contact_person_name', 'Jane Doe')
        ->assertSet('phone_number', '9876543210')
        ->assertSet('currentStep', 2)
        ->assertSet('completedSteps', [1]);
});

test('draft is marked as completed when form is submitted', function () {
    $component = Livewire::test(PublicExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('email', 'test@example.com')
        ->set('currentStep', 3);

    $draftId = $component->get('draftId');

    // Mock file uploads for step 3
    $component
        ->set('photos', [
            \Livewire\Features\SupportFileUploads\TemporaryUploadedFile::fake()->image('photo1.jpg'),
            \Livewire\Features\SupportFileUploads\TemporaryUploadedFile::fake()->image('photo2.jpg'),
            \Livewire\Features\SupportFileUploads\TemporaryUploadedFile::fake()->image('photo3.jpg'),
        ])
        ->call('nextStep')
        ->set('facia_name', 'TEST COMPANY')
        ->call('submit');

    $draft = DraftExhibitor::find($draftId);
    expect($draft->is_completed)->toBeTrue();
});

test('resume URL is generated correctly', function () {
    $draft = DraftExhibitor::create([
        'resume_token' => 'test-token-123',
        'last_activity_at' => now(),
    ]);

    $url = $draft->resume_url;

    expect($url)->toContain('/register-exhibitor')
        ->and($url)->toContain('resume=test-token-123');
});

test('completed drafts are not loaded', function () {
    $draft = DraftExhibitor::create([
        'resume_token' => DraftExhibitor::generateResumeToken(),
        'brand_name' => 'Completed Company',
        'is_completed' => true,
        'last_activity_at' => now(),
    ]);

    Livewire::test(PublicExhibitorForm::class, ['resume' => $draft->resume_token])
        ->assertSet('brand_name', '') // Should not load completed draft
        ->assertSet('draftId', function ($value) use ($draft) {
            return $value !== $draft->id; // Should create new draft
        });
});
