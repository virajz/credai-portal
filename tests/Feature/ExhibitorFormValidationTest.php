<?php

use App\Livewire\PublicExhibitorForm;
use Livewire\Livewire;

test('phone number must be exactly 10 digits', function () {
    // Test too short
    Livewire::test(PublicExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->call('nextStep') // Complete step 1
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '123') // Too short
        ->call('nextStep')
        ->assertHasErrors(['phone_number' => 'digits']);

    // Test too long
    Livewire::test(PublicExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->call('nextStep')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '12345678901') // Too long
        ->call('nextStep')
        ->assertHasErrors(['phone_number' => 'digits']);

    // Test contains letters
    Livewire::test(PublicExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->call('nextStep')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '98765abcde') // Contains letters
        ->call('nextStep')
        ->assertHasErrors(['phone_number' => 'digits']);

    // Test valid phone number
    Livewire::test(PublicExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->call('nextStep')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '9876543210') // Valid
        ->call('nextStep')
        ->assertHasNoErrors('phone_number');
});

test('phone number is required', function () {
    Livewire::test(PublicExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', '123 Test Street')
        ->set('city', 'Surat')
        ->call('nextStep')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '')
        ->call('nextStep')
        ->assertHasErrors(['phone_number' => 'required']);
});
