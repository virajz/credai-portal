<?php

declare(strict_types=1);

use App\Models\Exhibitor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseHas;

test('guests can access public exhibitor registration form', function () {
    $response = $this->get(route('exhibitor.public.register'));

    $response->assertSuccessful();
    $response->assertSee('Exhibitor Information Form');
});

test('public form can be submitted without authentication', function () {
    Storage::fake('public');

    $logo = UploadedFile::fake()->image('logo.png', 500, 500)->size(1024);
    $brochure = UploadedFile::fake()->create('brochure.pdf', 2048);
    $photos = [
        UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(1024),
        UploadedFile::fake()->image('photo2.jpg', 800, 600)->size(1024),
        UploadedFile::fake()->image('photo3.jpg', 800, 600)->size(1024),
    ];

    Livewire::test('public-exhibitor-form')
        ->set('brand_name', 'Test Company Ltd')
        ->set('office_address', '123 Test Street, Test Area, Test City')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('email', 'john@testcompany.com')
        ->set('website', 'https://testcompany.com')
        ->set('logo', $logo)
        ->set('brochure', $brochure)
        ->set('photos', $photos)
        ->set('photo_labels', ['Label 1', 'Label 2', 'Label 3'])
        ->set('video_url', 'https://youtube.com/watch?v=test')
        ->set('social_media_links.facebook', 'https://facebook.com/testcompany')
        ->set('social_media_links.instagram', 'https://instagram.com/testcompany')
        ->set('social_media_links.linkedin', 'https://linkedin.com/company/testcompany')
        ->set('facia_name', 'TEST COMPANY LTD')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('exhibitor.public.thank-you'));

    assertDatabaseHas('exhibitors', [
        'brand_name' => 'Test Company Ltd',
        'city' => 'Surat',
        'contact_person_name' => 'John Doe',
        'phone_number' => '9876543210',
        'email' => 'john@testcompany.com',
        'facia_name' => 'TEST COMPANY LTD',
    ]);

    $exhibitor = Exhibitor::where('brand_name', 'Test Company Ltd')->first();
    expect($exhibitor->logo_path)->not->toBeNull();
    expect($exhibitor->brochure_path)->not->toBeNull();
    expect(Storage::disk('public')->exists($exhibitor->logo_path))->toBeTrue();
    expect(Storage::disk('public')->exists($exhibitor->brochure_path))->toBeTrue();
});

test('public form redirects to thank you page after submission', function () {
    $response = $this->get(route('exhibitor.public.thank-you'));

    $response->assertSuccessful();
    $response->assertSee('Thank You for Registering!');
    $response->assertSee('What', false);
});

test('public form persists data to database correctly', function () {
    Storage::fake('public');

    $photos = [
        UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(1024),
        UploadedFile::fake()->image('photo2.jpg', 800, 600)->size(1024),
        UploadedFile::fake()->image('photo3.jpg', 800, 600)->size(1024),
    ];

    Livewire::test('public-exhibitor-form')
        ->set('brand_name', 'Another Test Company')
        ->set('office_address', '456 Another Street')
        ->set('city', 'Ahmedabad')
        ->set('contact_person_name', 'Jane Smith')
        ->set('phone_number', '1234567890')
        ->set('photos', $photos)
        ->set('facia_name', 'ANOTHER TEST COMPANY')
        ->call('submit')
        ->assertHasNoErrors();

    $exhibitor = Exhibitor::where('brand_name', 'Another Test Company')->first();

    expect($exhibitor)->not->toBeNull();
    expect($exhibitor->city)->toBe('Ahmedabad');
    expect($exhibitor->contact_person_name)->toBe('Jane Smith');
});
