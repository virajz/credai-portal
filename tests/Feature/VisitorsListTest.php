<?php

use App\Livewire\VisitorsList;
use App\Models\User;
use App\Models\Visitor;
use Livewire\Livewire;

test('visitors list page requires authentication', function () {
    $response = $this->get(route('visitors.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view visitors list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('visitors.index'))
        ->assertOk()
        ->assertSeeLivewire(VisitorsList::class);
});

test('visitors list displays visitors data', function () {
    $user = User::factory()->create();
    $visitor = Visitor::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
    ]);

    $this->actingAs($user)
        ->get(route('visitors.index'))
        ->assertSee('John Doe')
        ->assertSee('john@example.com');
});

test('visitors list can be searched', function () {
    $user = User::factory()->create();
    $visitor1 = Visitor::factory()->create(['name' => 'John Doe']);
    $visitor2 = Visitor::factory()->create(['name' => 'Jane Smith']);

    $component = Livewire::test(VisitorsList::class)
        ->actingAs($user)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('visitors can be deleted', function () {
    $user = User::factory()->create();
    $visitor = Visitor::factory()->create(['name' => 'Test Visitor']);

    expect(Visitor::count())->toBe(1);

    Livewire::test(VisitorsList::class)
        ->actingAs($user)
        ->call('confirmDelete', $visitor->id)
        ->call('deleteVisitor');

    expect(Visitor::count())->toBe(0);
});
