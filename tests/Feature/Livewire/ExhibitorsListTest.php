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
    $user = User::factory()->create();
    $this->actingAs($user);

    $exhibitor1 = Exhibitor::factory()->create(['city' => 'Surat', 'brand_name' => 'Surat Company']);
    $exhibitor2 = Exhibitor::factory()->create(['city' => 'Ahmedabad', 'brand_name' => 'Ahmedabad Company']);

    Livewire::test(ExhibitorsList::class)
        ->set('search', 'Surat')
        ->assertSee('Surat Company')
        ->assertDontSee('Ahmedabad Company');
});

test('exhibitors list can filter by city', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $exhibitor1 = Exhibitor::factory()->create(['city' => 'Surat', 'brand_name' => 'Surat Company']);
    $exhibitor2 = Exhibitor::factory()->create(['city' => 'Ahmedabad', 'brand_name' => 'Ahmedabad Company']);

    Livewire::test(ExhibitorsList::class)
        ->set('cityFilter', 'Surat')
        ->assertSee('Surat Company')
        ->assertDontSee('Ahmedabad Company');
});

test('exhibitors list can sort by brand name ascending', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Exhibitor::factory()->create(['brand_name' => 'Zebra Developers']);
    Exhibitor::factory()->create(['brand_name' => 'Alpha Builders']);

    Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'brand_name')
        ->assertSeeInOrder(['Alpha Builders', 'Zebra Developers']);
});

test('exhibitors list can sort by brand name descending', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Exhibitor::factory()->create(['brand_name' => 'Zebra Developers']);
    Exhibitor::factory()->create(['brand_name' => 'Alpha Builders']);

    Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'brand_name')
        ->call('sortByColumn', 'brand_name')
        ->assertSeeInOrder(['Zebra Developers', 'Alpha Builders']);
});

test('exhibitors list can sort by city', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Exhibitor::factory()->create(['city' => 'Surat', 'brand_name' => 'Surat Company']);
    Exhibitor::factory()->create(['city' => 'Ahmedabad', 'brand_name' => 'Ahmedabad Company']);

    Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'city')
        ->assertSeeInOrder(['Ahmedabad Company', 'Surat Company']);
});

test('exhibitors list can sort by created at', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $old = Exhibitor::factory()->create([
        'brand_name' => 'Old Company',
        'created_at' => now()->subDays(5),
    ]);
    $new = Exhibitor::factory()->create([
        'brand_name' => 'New Company',
        'created_at' => now(),
    ]);

    Livewire::test(ExhibitorsList::class)
        ->call('sortByColumn', 'created_at')
        ->assertSeeInOrder(['Old Company', 'New Company']);
});

test('exhibitors list resets page when searching', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Exhibitor::factory()->count(15)->create();

    Livewire::test(ExhibitorsList::class)
        ->call('gotoPage', 2, 'page')
        ->assertSet('search', '')
        ->set('search', 'test')
        ->assertSuccessful();
});

test('exhibitors list resets page when filtering by city', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Exhibitor::factory()->count(15)->create();

    Livewire::test(ExhibitorsList::class)
        ->call('gotoPage', 2, 'page')
        ->assertSet('cityFilter', '')
        ->set('cityFilter', 'Surat')
        ->assertSuccessful();
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
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create 15 exhibitors with unique brand names
    Exhibitor::factory()->create(['brand_name' => 'Alpha Company']);
    Exhibitor::factory()->create(['brand_name' => 'Beta Company']);
    Exhibitor::factory()->create(['brand_name' => 'Gamma Company']);
    Exhibitor::factory()->create(['brand_name' => 'Delta Company']);
    Exhibitor::factory()->create(['brand_name' => 'Epsilon Company']);
    Exhibitor::factory()->create(['brand_name' => 'Zeta Company']);
    Exhibitor::factory()->create(['brand_name' => 'Eta Company']);
    Exhibitor::factory()->create(['brand_name' => 'Theta Company']);
    Exhibitor::factory()->create(['brand_name' => 'Iota Company']);
    Exhibitor::factory()->create(['brand_name' => 'Kappa Company']);
    Exhibitor::factory()->create(['brand_name' => 'Lambda Company']);
    Exhibitor::factory()->create(['brand_name' => 'Mu Company']);
    Exhibitor::factory()->create(['brand_name' => 'Nu Company']);
    Exhibitor::factory()->create(['brand_name' => 'Xi Company']);
    Exhibitor::factory()->create(['brand_name' => 'Omicron Company']);

    Livewire::test(ExhibitorsList::class)
        ->assertSee('Kappa Company')  // 10th item, last on page 1
        ->assertDontSee('Lambda Company')  // 11th item, first on page 2
        ->call('gotoPage', 2, 'page')
        ->assertSee('Lambda Company')
        ->assertSee('Omicron Company')  // Last item
        ->assertDontSee('Kappa Company');  // Should not see page 1 items
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
    $user = User::factory()->create();
    $this->actingAs($user);

    Exhibitor::factory()->create(['city' => 'Surat']);
    Exhibitor::factory()->create(['city' => 'Surat']);
    Exhibitor::factory()->create(['city' => 'Ahmedabad']);

    Livewire::test(ExhibitorsList::class)
        ->assertSee('Surat')
        ->assertSee('Ahmedabad');
});
