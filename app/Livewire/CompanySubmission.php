<?php

namespace App\Livewire;

use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanySubmission extends Component
{
    use WithFileUploads;

    public Company $company;

    #[Url]
    public string $tab = 'overview';

    public ?int $selectedProjectIndex = null;

    public $previewLogo;

    public function mount(Company $company): void
    {
        // Check if company has a submission
        if (! $company->has_submitted) {
            abort(404, 'No submission found for this company');
        }

        $this->company = $company->load(['exhibitor.projects', 'draftExhibitor']);
    }

    public function selectProject(int $index): void
    {
        $this->selectedProjectIndex = $index;
    }

    public function uploadPreviewLogo(): void
    {
        $this->validate([
            'previewLogo' => 'required|image|mimes:jpeg,jpg,png|max:10240',
        ]);

        if ($this->company->exhibitor) {
            // Delete old preview logo if it exists
            if ($this->company->exhibitor->preview_logo && \Storage::disk('public')->exists($this->company->exhibitor->preview_logo)) {
                \Storage::disk('public')->delete($this->company->exhibitor->preview_logo);
            }

            $path = $this->previewLogo->storeAs(
                'exhibitors/preview-logos',
                time().'_'.$this->previewLogo->getClientOriginalName(),
                'public'
            );

            $this->company->exhibitor->update([
                'preview_logo' => $path,
            ]);

            $this->reset('previewLogo');

            Flux::toast(
                heading: 'Preview logo uploaded',
                text: 'The preview logo has been uploaded successfully.',
                variant: 'success'
            );
        }
    }

    public function removePreviewLogo(): void
    {
        if ($this->company->exhibitor && $this->company->exhibitor->preview_logo) {
            // Delete the file from storage
            if (\Storage::disk('public')->exists($this->company->exhibitor->preview_logo)) {
                \Storage::disk('public')->delete($this->company->exhibitor->preview_logo);
            }

            // Clear the preview_logo column
            $this->company->exhibitor->update([
                'preview_logo' => null,
            ]);

            Flux::toast(
                heading: 'Preview logo removed',
                text: 'The preview logo has been removed successfully.',
                variant: 'success'
            );
        }
    }

    #[Title('Company Submission')]
    public function render()
    {
        return view('livewire.company-submission');
    }
}
