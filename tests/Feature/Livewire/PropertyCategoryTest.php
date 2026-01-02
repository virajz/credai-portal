<?php

use App\Livewire\PropertyCategory;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(PropertyCategory::class)
        ->assertStatus(200);
});
