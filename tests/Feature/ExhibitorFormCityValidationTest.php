<?php

declare(strict_types=1);

use App\Livewire\ExhibitorForm;
use App\Models\User;
use Livewire\Livewire;

test('exhibitor form validates city must be from dropdown options', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ExhibitorForm::class)
        ->set('brand_name', 'Test Company')
        ->set('office_address', 'Test Address')
        ->set('city', 'Invalid City')
        ->set('contact_person_name', 'John Doe')
        ->set('phone_number', '1234567890')
        ->set('facia_name', 'TEST COMPANY')
        ->call('submit')
        ->assertHasErrors(['city' => 'in']);
});

test('exhibitor form accepts all valid city options', function () {
    $this->actingAs(User::factory()->create());

    $validCities = ['Surat', 'Navsari', 'Ahmedabad', 'Baroda', 'Others'];

    foreach ($validCities as $city) {
        Livewire::test(ExhibitorForm::class)
            ->set('brand_name', 'Test Company')
            ->set('office_address', 'Test Address')
            ->set('city', $city)
            ->set('contact_person_name', 'John Doe')
            ->set('phone_number', '1234567890')
            ->set('facia_name', 'TEST COMPANY')
            ->call('submit')
            ->assertHasNoErrors('city');
    }
});
