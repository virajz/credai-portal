<?php

declare(strict_types=1);

use App\Livewire\ExhibitorsList;
use App\Models\Exhibitor;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\get;

test('guests cannot access exhibitors list', function () {
    get(route('exhibitors.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view exhibitors list', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    get(route('exhibitors.index'))
        ->assertOk()
        ->assertSeeLivewire(ExhibitorsList::class);
});

test('exhibitors list displays all exhibitors', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $exhibitors = Exhibitor::factory()->count(3)->create();

    Livewire::test(ExhibitorsList::class)
        ->assertSee($exhibitors[0]->brand_name)
        ->assertSee($exhibitors[1]->brand_name)
        ->assertSee($exhibitors[2]->brand_name);
});

test('exhibitors list can search by brand name', function () {
    $exhibitor1 = Exhibitor::factory()->create(['brand_name' => 'ABC Developers']);
    $exhibitor2 = Exhibitor::factory()->create(['brand_name' => 'XYZ Builders']);

    Livewire::test(ExhibitorsList::class)
        ->set('search', 'ABC')
        ->assertSee('ABC Developers')
        ->assertDontSee('XYZ Builders');
});

test('exhibitors list can search by contact person name', function () {
    $exhibitor1 = Exhibitor::factory()->create(['contact_person_name' => 'John Doe']);
    $exhibitor2 = Exhibitor::factory()->create(['contact_person_name' => 'Jane Smith']);

    Livewire::test(ExhibitorsList::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('exhibitors list can search by email', function () {
    $exhibitor1 = Exhibitor::factory()->create(['email' => 'john@example.com']);
    $exhibitor2 = Exhibitor::factory()->create(['email' => 'jane@example.com']);

    Livewire::test(ExhibitorsList::class)
        ->set('search', 'john@example')
        ->assertSee('john@example.com')
        ->assertDontSee('jane@example.com');
});

test('exhibitors list can search by phone number', function () {
    $exhibitor1 = Exhibitor::factory()->create(['phone_number' => '+91 98765 43210']);
    $exhibitor2 = Exhibitor::factory()->create(['phone_number' => '+91 12345 67890']);

    Livewire::test(ExhibitorsList::class)
        ->set('search', '98765')
        ->assertSee('+91 98765 43210')
        ->assertDontSee('+91 12345 67890');
});

test('exhibitors list can search by city', function () {
    $exhibitor1 = Exhibitor::factory()->create(['city' => 'Surat']);
    $exhibitor2 = Exhibitor::factory()->create(['city' => 'Ahmedabad']);

    Livewire::test(ExhibitorsList::class)
        ->set('search', 'Surat')
        ->assertSee('Surat')
        ->assertDontSee('Ahmedabad');
});

test('exhibitors list can filter by city', function () {
    $exhibitor1 = Exhibitor::factory()->create(['city' => 'Surat']);
    $exhibitor2 = Exhibitor::factory()->create(['city' => 'Ahmedabad']);

    Livewire::test(ExhibitorsList::class)
        ->set('cityFilter', 'Surat')
        ->assertSee('Surat')
        ->assertDontSee('Ahmedabad');
});

test('exhibitors list can sort by brand name ascending', function () {
    Exhibitor::factory()->create(['brand_name' => 'Zebra Developers']);
    Exhibitor::factory()->create(['brand_name' => 'Alpha Builders']);

    $component = Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'brand_name');

    $exhibitors = $component->get('exhibitors');
    expect($exhibitors->first()->brand_name)->toBe('Alpha Builders')
        ->and($exhibitors->last()->brand_name)->toBe('Zebra Developers');
});

test('exhibitors list can sort by brand name descending', function () {
    Exhibitor::factory()->create(['brand_name' => 'Zebra Developers']);
    Exhibitor::factory()->create(['brand_name' => 'Alpha Builders']);

    $component = Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'brand_name')
        ->call('sortByColumn', 'brand_name');

    $exhibitors = $component->get('exhibitors');
    expect($exhibitors->first()->brand_name)->toBe('Zebra Developers')
        ->and($exhibitors->last()->brand_name)->toBe('Alpha Builders');
});

test('exhibitors list can sort by city', function () {
    Exhibitor::factory()->create(['city' => 'Surat']);
    Exhibitor::factory()->create(['city' => 'Ahmedabad']);

    $component = Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'city');

    $exhibitors = $component->get('exhibitors');
    expect($exhibitors->first()->city)->toBe('Ahmedabad')
        ->and($exhibitors->last()->city)->toBe('Surat');
});

test('exhibitors list can sort by created at', function () {
    $old = Exhibitor::factory()->create(['created_at' => now()->subDays(5)]);
    $new = Exhibitor::factory()->create(['created_at' => now()]);

    $component = Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'created_at');

    $exhibitors = $component->get('exhibitors');
    expect($exhibitors->first()->id)->toBe($old->id)
        ->and($exhibitors->last()->id)->toBe($new->id);
});

test('exhibitors list resets page when searching', function () {
    Exhibitor::factory()->count(15)->create();

    Livewire::test(ExhibitorsList::class)
        ->set('page', 2)
        ->set('search', 'test')
        ->assertSet('page', 1);
});

test('exhibitors list resets page when filtering by city', function () {
    Exhibitor::factory()->count(15)->create();

    Livewire::test(ExhibitorsList::class)
        ->set('page', 2)
        ->set('cityFilter', 'Surat')
        ->assertSet('page', 1);
});

test('exhibitors list can clear all filters', function () {
    Livewire::test(ExhibitorsList::class)
        ->set('search', 'test')
        ->set('cityFilter', 'Surat')
        ->set('sortBy', 'brand_name')
        ->set('sortDirection', 'asc')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('cityFilter', '')
        ->assertSet('sortBy', 'created_at')
        ->assertSet('sortDirection', 'desc');
});

test('exhibitors list shows empty state when no exhibitors exist', function () {
    Livewire::test(ExhibitorsList::class)
        ->assertSee('No exhibitors found')
        ->assertSee('Get started by adding your first exhibitor');
});

test('exhibitors list shows filtered empty state', function () {
    Exhibitor::factory()->create(['brand_name' => 'ABC Developers']);

    Livewire::test(ExhibitorsList::class)
        ->set('search', 'nonexistent')
        ->assertSee('No exhibitors found')
        ->assertSee('Try adjusting your search or filter criteria');
});

test('exhibitors list paginates results', function () {
    Exhibitor::factory()->count(15)->create();

    $component = Livewire::test(ExhibitorsList::class);

    $exhibitors = $component->get('exhibitors');
    expect($exhibitors)->toHaveCount(10)
        ->and($exhibitors->hasPages())->toBeTrue();
});

test('exhibitors list shows add exhibitor button', function () {
    Livewire::test(ExhibitorsList::class)
        ->assertSee('Add Exhibitor');
});

test('exhibitors list displays exhibitor details correctly', function () {
    $exhibitor = Exhibitor::factory()->create([
        'brand_name' => 'Test Brand',
        'facia_name' => 'TEST BRAND',
        'city' => 'Surat',
        'contact_person_name' => 'John Doe',
        'phone_number' => '+91 98765 43210',
        'email' => 'john@example.com',
    ]);

    Livewire::test(ExhibitorsList::class)
        ->assertSee('Test Brand')
        ->assertSee('TEST BRAND')
        ->assertSee('Surat')
        ->assertSee('John Doe')
        ->assertSee('+91 98765 43210')
        ->assertSee('john@example.com');
});

test('exhibitors list shows distinct cities for filter', function () {
    Exhibitor::factory()->create(['city' => 'Surat']);
    Exhibitor::factory()->create(['city' => 'Surat']);
    Exhibitor::factory()->create(['city' => 'Ahmedabad']);

    $component = Livewire::test(ExhibitorsList::class);

    $cities = $component->get('cities');
    expect($cities)->toHaveCount(2)
        ->and($cities->contains('Surat'))->toBeTrue()
        ->and($cities->contains('Ahmedabad'))->toBeTrue();
});
