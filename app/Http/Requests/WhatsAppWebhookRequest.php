<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WhatsAppWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'messageId' => ['required', 'string'],
            'channel' => ['required', 'string', 'in:whatsapp'],
            'from' => ['required', 'string'],
            'to' => ['required', 'string'],
            'receivedAt' => ['required', 'date'],
            'content' => ['required', 'array'],
            'content.contentType' => ['required', 'string'],
            'content.text' => ['nullable', 'string'],
            'whatsapp' => ['required', 'array'],
            'whatsapp.senderName' => ['required', 'string'],
            'timestamp' => ['required', 'date'],
            'event' => ['required', 'string'],
            'isin24window' => ['required', 'boolean'],
            'isResponded' => ['required', 'boolean'],
            'UserResponse' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'messageId.required' => 'Message ID is required.',
            'channel.required' => 'Channel is required.',
            'channel.in' => 'Channel must be whatsapp.',
            'from.required' => 'Sender number is required.',
            'to.required' => 'Recipient number is required.',
            'receivedAt.required' => 'Received timestamp is required.',
            'receivedAt.date' => 'Received timestamp must be a valid date.',
            'content.required' => 'Message content is required.',
            'content.contentType.required' => 'Content type is required.',
            'whatsapp.required' => 'WhatsApp metadata is required.',
            'whatsapp.senderName.required' => 'Sender name is required.',
            'timestamp.required' => 'Timestamp is required.',
            'timestamp.date' => 'Timestamp must be a valid date.',
            'event.required' => 'Event type is required.',
            'isin24window.required' => '24-hour window status is required.',
            'isin24window.boolean' => '24-hour window status must be true or false.',
            'isResponded.required' => 'Response status is required.',
            'isResponded.boolean' => 'Response status must be true or false.',
        ];
    }
}
