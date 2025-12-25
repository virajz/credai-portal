<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExhibitorRequest extends FormRequest
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
            // Company Details
            'brand_name' => ['required', 'string', 'max:255'],
            'office_address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:255'],
            'gst_number' => ['nullable', 'string', 'max:255'],
            'pan_number' => ['nullable', 'string', 'max:255'],

            // Contact Person
            'contact_person_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],

            // Branding & Media
            'logo' => ['nullable', 'file', 'extensions:png,jpg,jpeg,pdf,cdr', 'max:5120'],
            'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'photos' => ['nullable', 'array', 'min:3', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'photo_labels' => ['nullable', 'array'],
            'photo_labels.*' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'social_media_links' => ['nullable', 'array'],
            'social_media_links.facebook' => ['nullable', 'url', 'max:255'],
            'social_media_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_media_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_media_links.youtube' => ['nullable', 'url', 'max:255'],

            // Exhibition Display
            'facia_name' => ['required', 'string', 'max:255'],
            'additional_details' => ['nullable', 'string', 'max:1000'],
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
            'brand_name.required' => 'Please enter your company or brand name.',
            'brand_name.max' => 'Brand name should not exceed 255 characters.',

            'office_address.required' => 'Company address is required for CREDAI records.',
            'office_address.max' => 'Address should not exceed 1000 characters.',

            'city.required' => 'Please specify your main business location.',
            'city.max' => 'City name should not exceed 255 characters.',

            'gst_number.max' => 'GST number should not exceed 255 characters.',
            'pan_number.max' => 'PAN card number should not exceed 255 characters.',

            'contact_person_name.required' => 'Please provide the main contact person\'s name.',
            'contact_person_name.max' => 'Contact person name should not exceed 255 characters.',

            'phone_number.required' => 'Mobile number is required for event coordination.',
            'phone_number.max' => 'Phone number should not exceed 20 characters.',

            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email should not exceed 255 characters.',

            'website.url' => 'Please provide a valid website URL.',
            'website.max' => 'Website URL should not exceed 255 characters.',

            'logo.file' => 'Logo must be a file.',
            'logo.mimes' => 'Logo must be a PNG, JPG, PDF, or CDR file.',
            'logo.max' => 'Logo file size should not exceed 5MB.',

            'brochure.file' => 'Brochure must be a file.',
            'brochure.mimes' => 'Brochure must be a PDF file.',
            'brochure.max' => 'Brochure file size should not exceed 10MB.',

            'photos.min' => 'Please upload at least 3 photos.',
            'photos.max' => 'You can upload a maximum of 5 photos.',
            'photos.*.image' => 'All photos must be image files.',
            'photos.*.mimes' => 'Photos must be PNG or JPG files.',
            'photos.*.max' => 'Each photo should not exceed 2MB.',

            'photo_labels.*.max' => 'Photo label should not exceed 255 characters.',

            'video_url.url' => 'Please provide a valid video URL (YouTube or Vimeo).',
            'video_url.max' => 'Video URL should not exceed 255 characters.',

            'social_media_links.facebook.url' => 'Please provide a valid Facebook URL.',
            'social_media_links.linkedin.url' => 'Please provide a valid LinkedIn URL.',
            'social_media_links.instagram.url' => 'Please provide a valid Instagram URL.',
            'social_media_links.youtube.url' => 'Please provide a valid YouTube URL.',

            'facia_name.required' => 'Please provide the name for your booth fascia board.',
            'facia_name.max' => 'Facia name should not exceed 255 characters.',
            'additional_details.max' => 'Additional details should not exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'brand_name' => 'company/brand name',
            'office_address' => 'company address',
            'city' => 'city',
            'contact_person_name' => 'contact person',
            'phone_number' => 'mobile number',
            'email' => 'email',
            'website' => 'website',
            'logo' => 'company logo',
            'brochure' => 'company brochure',
            'photos' => 'images',
            'video_url' => 'video URL',
            'facia_name' => 'facia name',
        ];
    }
}
