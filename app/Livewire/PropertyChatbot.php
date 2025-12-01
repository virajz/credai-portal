<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ChatService;
use Livewire\Component;

class PropertyChatbot extends Component
{
    public bool $isOpen = false;

    public string $message = '';

    public array $messages = [];

    public bool $isLoading = false;

    public function mount(): void
    {
        $this->messages = [
            [
                'role' => 'assistant',
                'content' => 'Hello! 👋 I\'m your CREDAI property assistant. I can help you find properties based on your requirements. Tell me what you\'re looking for - area, budget, type of property, etc.',
            ],
        ];
    }

    public function toggleChat(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function sendMessage(): void
    {
        if (trim($this->message) === '') {
            return;
        }

        $userMessage = trim($this->message);
        $this->message = '';

        // Add user message
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        // Set loading state BEFORE making the API call
        $this->isLoading = true;

        // Force Livewire to update the UI
        $this->dispatch('message-sent');

        try {
            $chatService = new ChatService;

            $conversationHistory = array_slice($this->messages, 1, -1);

            $response = $chatService->chat($userMessage, $conversationHistory);

            // Add assistant message with properties
            $assistantMessage = [
                'role' => 'assistant',
                'content' => $response['response'] ?: 'I apologize, I couldn\'t process that request. Please try rephrasing your question.',
            ];

            // Only add properties if they exist and are not empty
            if (! empty($response['properties'])) {
                $assistantMessage['properties'] = $response['properties'];
            }

            $this->messages[] = $assistantMessage;

            // Log for debugging
            logger()->info('Chatbot response', [
                'response_text' => $response['response'],
                'properties_count' => count($response['properties'] ?? []),
                'properties' => $response['properties'] ?? [],
            ]);
        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'I apologize, but I encountered an error. Please try again.',
            ];

            logger()->error('Chatbot error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function clearChat(): void
    {
        $this->mount();
    }

    public function render()
    {
        return view('livewire.property-chatbot');
    }
}
