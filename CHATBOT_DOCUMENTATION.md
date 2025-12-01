# Property Chatbot Feature

## Overview

The Property Chatbot is an AI-powered assistant that helps visitors find properties based on their requirements using natural language. It's built with PrismPHP, Groq's Llama 3.3 70B model, and Livewire.

## Features

-   **Natural Language Understanding**: Users can describe their requirements in plain English
-   **Smart Property Search**: AI uses tool calling to search the database based on criteria
-   **Real-time Responses**: Fast responses powered by Groq Cloud API
-   **Conversational Interface**: Maintains chat history for contextual responses
-   **No Hallucination**: Only provides information from the actual database

## Architecture

### Components

#### 1. ChatService (`app/Services/ChatService.php`)

Handles all AI interactions and tool calling logic:

-   Configures PrismPHP with Groq provider
-   Defines search_properties tool for database queries
-   Builds conversation context
-   Processes AI responses

#### 2. PropertyChatbot Component (`app/Livewire/PropertyChatbot.php`)

Livewire component managing the chat UI:

-   Toggle chat window
-   Send/receive messages
-   Maintain conversation history
-   Loading states

#### 3. View (`resources/views/livewire/property-chatbot.blade.php`)

Floating chat interface with:

-   Fixed position chat button
-   Collapsible chat window
-   Message history
-   Input field with send button
-   Loading indicators

## Tool Calling

The chatbot uses PrismPHP's tool calling feature to search properties:

### search_properties Tool Parameters:

-   `area`: Location/area (e.g., Vesu, Adajan)
-   `category`: Property type (Residential, Commercial, Plotting)
-   `budget_min`: Minimum budget (e.g., "20L", "50L", "1Cr")
-   `budget_max`: Maximum budget
-   `status`: Project status (Ongoing, Completed, Upcoming)

### Example Queries:

-   "I need to buy a home in Vesu area, my budget is 50L"
-   "Show me commercial properties in Adajan under 2 crores"
-   "Looking for residential plotting projects"

## Configuration

### Environment Variables

```env
GROQ_API_KEY=your_groq_api_key_here
```

### Prism Configuration

Configured in `config/prism.php`:

```php
'groq' => [
    'api_key' => env('GROQ_API_KEY', ''),
    'url' => env('GROQ_URL', 'https://api.groq.com/openai/v1'),
],
```

## Usage

The chatbot is automatically included on all visitor-facing pages via the front layout (`components/layouts/front.blade.php`).

### For Users:

1. Click the chat icon in the bottom-right corner
2. Type your property requirements
3. Get instant AI-powered property recommendations
4. Click on property URLs to view details

### For Developers:

```php
// Use the ChatService directly
$service = new ChatService();
$response = $service->chat('I need a property in Vesu', []);
```

## Testing

Tests are located in:

-   `tests/Feature/PropertyChatbotTest.php` - UI component tests
-   `tests/Feature/ChatServiceTest.php` - Service and tool calling tests

Run tests:

```bash
php artisan test --filter="PropertyChatbot|ChatService"
```

## Customization

### Modify System Prompt

Edit `getSystemPrompt()` in `ChatService.php` to change AI behavior.

### Add More Search Parameters

Extend `createSearchPropertiesTool()` method with additional parameters.

### Change UI Design

Modify `resources/views/livewire/property-chatbot.blade.php` to customize appearance.

## Performance

-   **Model**: Llama 3.3 70B Versatile via Groq
-   **Average Response Time**: 1-3 seconds
-   **Max Steps**: 5 (for complex multi-turn interactions)
-   **Search Limit**: 10 properties per query

## Security

-   Input sanitization via Livewire
-   Database queries use Eloquent ORM (SQL injection protected)
-   API key stored in environment variables
-   No sensitive data exposed to frontend

## Future Enhancements

Potential improvements:

-   [ ] Streaming responses for faster perceived performance
-   [ ] Save chat history to database for analytics
-   [ ] Multi-language support
-   [ ] Voice input/output
-   [ ] Property comparison feature
-   [ ] Schedule viewing appointments directly through chat
-   [ ] Integration with exhibitor contact forms

## Troubleshooting

### Chatbot Not Responding

1. Verify `GROQ_API_KEY` is set in `.env`
2. Check network connectivity
3. Review Laravel logs: `storage/logs/laravel.log`

### Search Returns No Results

1. Verify projects exist in database
2. Check search parameters are matching database format
3. Review tool calling logic in `ChatService.php`

### UI Issues

1. Clear browser cache
2. Run `npm run build`
3. Check for JavaScript console errors

## Credits

Built with:

-   [PrismPHP](https://prismphp.com) - Laravel AI integration
-   [Groq Cloud](https://groq.com) - Ultra-fast LLM inference
-   [Livewire](https://livewire.laravel.com) - Reactive UI components
-   [Flux UI](https://flux.laravel.com) - Design system
