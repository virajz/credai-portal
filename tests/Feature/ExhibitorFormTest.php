<?php

use App\Livewire\ExhibitorForm;
use App\Models\Exhibitor;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/**
 * Helper function to generate minimum required photos for testing
 */
function getMinimumPhotos(): array
{
    return [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
        UploadedFile::fake()->image('photo3.jpg'),
    ];
}

test('guests cannot access exhibitor form', function () {
    $this->get(route('exhibitor.register'))->assertRedirect(route('login'));
});

test('authenticated users can view exhibitor form', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('exhibitor.register'))
        ->assertOk()
        ->assertSeeLivewire(ExhibitorForm::class);
});

test('exhibitor form requires all mandatory fields', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->call('submit')
        ->assertHasErrors([
            'brand_name' => 'required',
            'office_address' => 'required',
            'city' => 'required',
            'contact_person_name' => 'required',
            'phone_number' => 'required',
            'facia_name' => 'required',
        ]);
});

test('exhibitor form validates brand name max length', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', str_repeat('a', 256))
        ->call('submit')
        ->assertHasErrors(['brand_name' => 'max']);
});

test('exhibitor form validates email format', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('email', 'invalid-email')
        ->call('submit')
        ->assertHasErrors(['email' => 'email']);
});

test('exhibitor form validates website url format', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('website', 'not-a-url')
        ->call('submit')
        ->assertHasErrors(['website' => 'url']);
});

test('exhibitor form validates video url format', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('video_url', 'not-a-url')
        ->call('submit')
        ->assertHasErrors(['video_url' => 'url']);
});

test('exhibitor form validates social media urls', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('social_media_links.facebook', 'invalid')
        ->set('social_media_links.linkedin', 'invalid')
        ->set('social_media_links.instagram', 'invalid')
        ->call('submit')
        ->assertHasErrors([
            'social_media_links.facebook' => 'url',
            'social_media_links.linkedin' => 'url',
            'social_media_links.instagram' => 'url',
        ]);
});

test('exhibitor form validates logo file type', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $invalidFile = UploadedFile::fake()->create('document.txt', 100);

    Livewire::test(ExhibitorForm::class)
        ->set('logo', $invalidFile)
        ->call('submit')
        ->assertHasErrors(['logo' => 'mimes']);
});

test('exhibitor form validates logo file size', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $largeFile = UploadedFile::fake()->image('logo.png')->size(6000);

    Livewire::test(ExhibitorForm::class)
        ->set('logo', $largeFile)
        ->call('submit')
        ->assertHasErrors(['logo' => 'max']);
});

test('exhibitor form validates brochure file type', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $invalidFile = UploadedFile::fake()->image('image.png');

    Livewire::test(ExhibitorForm::class)
        ->set('brochure', $invalidFile)
        ->call('submit')
        ->assertHasErrors(['brochure' => 'mimes']);
});

test('exhibitor form validates brochure file size', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $largeFile = UploadedFile::fake()->create('brochure.pdf', 11000);

    Livewire::test(ExhibitorForm::class)
        ->set('brochure', $largeFile)
        ->call('submit')
        ->assertHasErrors(['brochure' => 'max']);
});

test('exhibitor form requires minimum 3 photos when photos are provided', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $photos = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
    ];

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', 'Test Address')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '1234567890')
        ->set('facia_name', 'TEST COMPANY')
        ->set('photos', $photos)
        ->call('submit')
        ->assertHasErrors('photos');
});

test('exhibitor form allows maximum 5 photos', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $photos = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
        UploadedFile::fake()->image('photo3.jpg'),
        UploadedFile::fake()->image('photo4.jpg'),
        UploadedFile::fake()->image('photo5.jpg'),
        UploadedFile::fake()->image('photo6.jpg'),
    ];

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', 'Test Address')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '1234567890')
        ->set('facia_name', 'TEST COMPANY')
        ->set('photos', $photos)
        ->call('submit')
        ->assertHasErrors(['photos' => 'max']);
});

test('exhibitor form creates exhibitor with valid data', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street, Building A')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('email', 'contact@abc.com')
        ->set('website', 'https://www.abc.com')
        ->set('video_url', 'https://youtube.com/watch?v=test')
        ->set('photos', getMinimumPhotos())
        ->set('facia_name', 'ABC DEVELOPERS')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $exhibitor = Exhibitor::first();
    expect($exhibitor)->not->toBeNull();
    expect($exhibitor->brand_name)->toBe('ABC Developers');
    expect($exhibitor->city)->toBe('Surat');
    expect($exhibitor->contact_person_name)->toBe('John Doe');
    expect($exhibitor->facia_name)->toBe('ABC DEVELOPERS');
});

test('exhibitor form creates exhibitor with only required fields', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('photos', getMinimumPhotos())
        ->set('facia_name', 'ABC DEVELOPERS')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(Exhibitor::count())->toBe(1);
});

test('exhibitor form stores logo file correctly', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $logo = UploadedFile::fake()->image('logo.png');

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('photos', getMinimumPhotos())
        ->set('facia_name', 'ABC DEVELOPERS')
        ->set('logo', $logo)
        ->call('submit')
        ->assertHasNoErrors();

    $exhibitor = Exhibitor::first();
    expect($exhibitor->logo_path)->not->toBeNull();
    expect(Storage::disk('public')->exists($exhibitor->logo_path))->toBeTrue();
});

test('exhibitor form stores brochure file correctly', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $brochure = UploadedFile::fake()->create('brochure.pdf', 1000);

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('photos', getMinimumPhotos())
        ->set('facia_name', 'ABC DEVELOPERS')
        ->set('brochure', $brochure)
        ->call('submit')
        ->assertHasNoErrors();

    $exhibitor = Exhibitor::first();
    expect($exhibitor->brochure_path)->not->toBeNull();
    expect(Storage::disk('public')->exists($exhibitor->brochure_path))->toBeTrue();
});

test('exhibitor form stores photos with labels correctly', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $photos = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
        UploadedFile::fake()->image('photo3.jpg'),
    ];

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('facia_name', 'ABC DEVELOPERS')
        ->set('photos', $photos)
        ->set('photo_labels', ['Showroom', 'Project Front', 'Interior'])
        ->call('submit')
        ->assertHasNoErrors();

    $exhibitor = Exhibitor::first();
    expect($exhibitor->photos)->toBeArray();
    expect($exhibitor->photos)->toHaveCount(3);
    expect($exhibitor->photos[0])->toHaveKeys(['path', 'label']);
    expect($exhibitor->photos[0]['label'])->toBe('Showroom');
});

test('exhibitor form stores social media links correctly', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('photos', getMinimumPhotos())
        ->set('facia_name', 'ABC DEVELOPERS')
        ->set('social_media_links.facebook', 'https://facebook.com/abc')
        ->set('social_media_links.linkedin', 'https://linkedin.com/company/abc')
        ->set('social_media_links.instagram', 'https://instagram.com/abc')
        ->call('submit')
        ->assertHasNoErrors();

    $exhibitor = Exhibitor::first();
    expect($exhibitor->social_media_links)->toBeArray();
    expect($exhibitor->social_media_links)->toHaveKeys(['facebook', 'linkedin', 'instagram']);
    expect($exhibitor->social_media_links['facebook'])->toBe('https://facebook.com/abc');
});

test('exhibitor form filters empty social media links', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('photos', getMinimumPhotos())
        ->set('facia_name', 'ABC DEVELOPERS')
        ->set('social_media_links.facebook', 'https://facebook.com/abc')
        ->set('social_media_links.linkedin', '')
        ->set('social_media_links.instagram', '')
        ->call('submit')
        ->assertHasNoErrors();

    $exhibitor = Exhibitor::first();
    expect($exhibitor->social_media_links)->toBeArray();
    expect($exhibitor->social_media_links)->toHaveKey('facebook');
    expect($exhibitor->social_media_links)->not->toHaveKey('linkedin');
    expect($exhibitor->social_media_links)->not->toHaveKey('instagram');
});

test('exhibitor form shows success message after submission', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'Test Brand')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'Test Person')
        ->set('phone_number', '9876543210')
        ->set('facia_name', 'TEST BRAND')
        ->set('logo', UploadedFile::fake()->image('logo.png', 100, 100))
        ->set('photos', [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg'),
            UploadedFile::fake()->image('photo3.jpg'),
        ])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(session('success'))->toBe('Exhibitor information submitted successfully!');
});

test('photo labels can be updated reactively without toJSON error', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'Test Brand')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'Test Person')
        ->set('phone_number', '9876543210')
        ->set('facia_name', 'TEST BRAND')
        ->set('photos', [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg'),
            UploadedFile::fake()->image('photo3.jpg'),
        ])
        ->set('photo_labels.0', 'First Photo Label')
        ->set('photo_labels.1', 'Second Photo Label')
        ->set('photo_labels.2', 'Third Photo Label')
        ->assertSet('photo_labels.0', 'First Photo Label')
        ->assertSet('photo_labels.1', 'Second Photo Label')
        ->assertSet('photo_labels.2', 'Third Photo Label')
        ->call('submit')
        ->assertHasNoErrors();

    $exhibitor = Exhibitor::latest()->first();
    expect($exhibitor->photos)->toHaveCount(3)
        ->and($exhibitor->photos[0]['label'])->toBe('First Photo Label')
        ->and($exhibitor->photos[1]['label'])->toBe('Second Photo Label')
        ->and($exhibitor->photos[2]['label'])->toBe('Third Photo Label');
});

test('step 3 validation prevents advancing without minimum 3 photos', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ExhibitorForm::class)
        ->set('currentStep', 3)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('facia_name', 'ABC DEVELOPERS')
        ->call('nextStep')
        ->assertHasErrors(['photos' => 'required']);
});

test('step 3 validation prevents advancing with less than 3 photos', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ExhibitorForm::class)
        ->set('currentStep', 3)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('facia_name', 'ABC DEVELOPERS')
        ->set('photos', [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg'),
        ])
        ->call('nextStep')
        ->assertHasErrors(['photos' => 'min']);
});

test('step 3 validation allows advancing with 3 or more photos', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ExhibitorForm::class)
        ->set('currentStep', 3)
        ->set('brand_name', 'ABC Developers')
        ->set('office_address', '123 Main Street')
        ->set('city', 'Surat')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210')
        ->set('facia_name', 'ABC DEVELOPERS')
        ->set('photos', getMinimumPhotos())
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 4);
});
