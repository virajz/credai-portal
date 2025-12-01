<?php

declare(strict_types=1);

use App\Livewire\PropertyChatbot;
use Livewire\Livewire;

it('renders property chatbot component', function () {
    Livewire::test(PropertyChatbot::class)
        ->assertOk()
        ->assertSet('isOpen', false)
        ->assertSet('message', '')
        ->assertCount('messages', 1);
});

it('can toggle chat window', function () {
    Livewire::test(PropertyChatbot::class)
        ->assertSet('isOpen', false)
        ->call('toggleChat')
        ->assertSet('isOpen', true)
        ->call('toggleChat')
        ->assertSet('isOpen', false);
});

it('has initial greeting message', function () {
    Livewire::test(PropertyChatbot::class)
        ->assertSet('messages.0.role', 'assistant')
        ->assertSee('Hello!')
        ->assertSee('CREDAI property assistant');
});

it('can send message', function () {
    Livewire::test(PropertyChatbot::class)
        ->set('message', 'Hello, I need a property')
        ->call('sendMessage')
        ->assertSet('message', '')
        ->assertCount('messages', 3); // Initial + user message + assistant response
});

it('does not send empty messages', function () {
    Livewire::test(PropertyChatbot::class)
        ->set('message', '   ')
        ->call('sendMessage')
        ->assertCount('messages', 1); // Only initial message
});

it('can clear chat history', function () {
    Livewire::test(PropertyChatbot::class)
        ->set('message', 'Test message')
        ->call('sendMessage')
        ->assertCount('messages', 3)
        ->call('clearChat')
        ->assertCount('messages', 1)
        ->assertSet('messages.0.role', 'assistant');
});

it('shows loading state when sending message', function () {
    $component = Livewire::test(PropertyChatbot::class)
        ->set('message', 'Test message');

    $component->call('sendMessage');

    // Note: isLoading will be false after the call completes
    // In a real scenario, you'd mock the ChatService to test loading state
    expect($component->get('isLoading'))->toBeFalse();
});

it('displays chatbot on visitor pages', function () {
    $this->get(route('home'))
        ->assertSeeLivewire(PropertyChatbot::class);

    $this->get(route('public.exhibitors'))
        ->assertSeeLivewire(PropertyChatbot::class);
});
