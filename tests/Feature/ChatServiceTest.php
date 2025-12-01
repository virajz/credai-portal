<?php

declare(strict_types=1);

use App\Models\Exhibitor;
use App\Models\Project;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->exhibitor = Exhibitor::factory()->create();
});

it('creates search properties tool', function () {
    $service = new ChatService;

    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('createSearchPropertiesTool');
    $method->setAccessible(true);

    $tool = $method->invoke($service);

    expect($tool)->toBeObject();
});

it('searches properties by area', function () {
    Project::factory()->create([
        'exhibitor_id' => $this->exhibitor->id,
        'name' => 'Vesu Heights',
        'area' => 'Vesu',
        'category' => 'Residential',
        'budget_range' => '50L - 1Cr',
    ]);

    Project::factory()->create([
        'exhibitor_id' => $this->exhibitor->id,
        'name' => 'Adajan Plaza',
        'area' => 'Adajan',
        'category' => 'Commercial',
        'budget_range' => '1Cr - 2Cr',
    ]);

    $service = new ChatService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('createSearchPropertiesTool');
    $method->setAccessible(true);

    $tool = $method->invoke($service);

    // Access the tool's handler
    $handler = $reflection->getProperty('handler');
    $handler->setAccessible(true);
    $toolHandler = $handler->getValue($tool);

    $result = $toolHandler('Vesu', null, null, null, null);
    $data = json_decode($result, true);

    expect($data['count'])->toBe(1);
    expect($data['projects'][0]['name'])->toBe('Vesu Heights');
    expect($data['projects'][0]['area'])->toBe('Vesu');
});

it('searches properties by category', function () {
    Project::factory()->create([
        'exhibitor_id' => $this->exhibitor->id,
        'name' => 'Commercial Plaza',
        'area' => 'Vesu',
        'category' => 'Commercial',
        'budget_range' => '1Cr - 2Cr',
    ]);

    Project::factory()->create([
        'exhibitor_id' => $this->exhibitor->id,
        'name' => 'Residential Homes',
        'area' => 'Adajan',
        'category' => 'Residential',
        'budget_range' => '50L - 1Cr',
    ]);

    $service = new ChatService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('createSearchPropertiesTool');
    $method->setAccessible(true);

    $tool = $method->invoke($service);
    $handler = $reflection->getProperty('handler');
    $handler->setAccessible(true);
    $toolHandler = $handler->getValue($tool);

    $result = $toolHandler(null, 'Commercial', null, null, null);
    $data = json_decode($result, true);

    expect($data['count'])->toBe(1);
    expect($data['projects'][0]['category'])->toBe('Commercial');
});

it('returns empty result when no properties match', function () {
    $service = new ChatService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('createSearchPropertiesTool');
    $method->setAccessible(true);

    $tool = $method->invoke($service);
    $handler = $reflection->getProperty('handler');
    $handler->setAccessible(true);
    $toolHandler = $handler->getValue($tool);

    $result = $toolHandler('NonExistentArea', null, null, null, null);
    $data = json_decode($result, true);

    expect($data['count'])->toBe(0);
    expect($data['message'])->toContain('No properties found');
});

it('includes exhibitor information in search results', function () {
    $project = Project::factory()->create([
        'exhibitor_id' => $this->exhibitor->id,
        'name' => 'Test Project',
        'area' => 'Vesu',
        'contact_person' => 'John Doe',
    ]);

    $service = new ChatService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('createSearchPropertiesTool');
    $method->setAccessible(true);

    $tool = $method->invoke($service);
    $handler = $reflection->getProperty('handler');
    $handler->setAccessible(true);
    $toolHandler = $handler->getValue($tool);

    $result = $toolHandler('Vesu', null, null, null, null);
    $data = json_decode($result, true);

    expect($data['projects'][0])->toHaveKeys([
        'id',
        'name',
        'area',
        'category',
        'budget_range',
        'exhibitor_name',
        'contact_person',
        'url',
    ]);
});

it('limits search results to 10 properties', function () {
    // Create 15 projects
    Project::factory()->count(15)->create([
        'exhibitor_id' => $this->exhibitor->id,
        'area' => 'Vesu',
    ]);

    $service = new ChatService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('createSearchPropertiesTool');
    $method->setAccessible(true);

    $tool = $method->invoke($service);
    $handler = $reflection->getProperty('handler');
    $handler->setAccessible(true);
    $toolHandler = $handler->getValue($tool);

    $result = $toolHandler('Vesu', null, null, null, null);
    $data = json_decode($result, true);

    expect($data['count'])->toBeLessThanOrEqual(10);
});

it('builds prompt correctly', function () {
    $service = new ChatService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('buildPrompt');
    $method->setAccessible(true);

    $conversationHistory = [
        ['role' => 'user', 'content' => 'Hello'],
        ['role' => 'assistant', 'content' => 'Hi there!'],
    ];

    $prompt = $method->invoke($service, 'New message', $conversationHistory);

    expect($prompt)->toContain('Previous conversation:');
    expect($prompt)->toContain('User: Hello');
    expect($prompt)->toContain('Assistant: Hi there!');
    expect($prompt)->toContain('User: New message');
});

it('builds prompt without history', function () {
    $service = new ChatService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('buildPrompt');
    $method->setAccessible(true);

    $prompt = $method->invoke($service, 'Hello', []);

    expect($prompt)->toBe('User: Hello');
    expect($prompt)->not->toContain('Previous conversation:');
});
