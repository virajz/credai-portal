<?php

namespace App\Livewire;

use Livewire\Attributes\Title;

class PublicExhibitorForm extends ExhibitorForm
{
    /**
     * Get the redirect route after successful submission
     */
    protected function getRedirectRoute(): string
    {
        return route('exhibitor.public.thank-you');
    }

    /**
     * Get the success message after submission
     */
    protected function getSuccessMessage(): string
    {
        return 'Thank you! Your exhibitor registration is complete. We will contact you shortly via WhatsApp.';
    }

    #[Title('Exhibitor Registration - CREDAI')]
    public function render()
    {
        return view('livewire.exhibitor-form')
            ->layout('components.layouts.public');
    }
}
