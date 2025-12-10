<?php

namespace App\Livewire;

use App\Models\Visitor;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VisitorsList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public ?int $visitorToDelete = null;

    public bool $showQrModal = false;

    public string $qrMedium = '';

    public string $qrCodeSvg = '';

    public string $generatedUrl = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function confirmDelete(int $visitorId): void
    {
        $this->visitorToDelete = $visitorId;
        $this->modal('delete-visitor')->show();
    }

    public function deleteVisitor(): void
    {
        if ($this->visitorToDelete) {
            $visitor = Visitor::findOrFail($this->visitorToDelete);
            $visitorName = $visitor->name;

            $visitor->delete();

            $this->visitorToDelete = null;
            $this->modal('delete-visitor')->close();

            Flux::toast(
                heading: 'Visitor deleted',
                text: "{$visitorName} has been removed successfully.",
                variant: 'success'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->visitorToDelete = null;
        $this->modal('delete-visitor')->close();
    }

    public function openQrModal(): void
    {
        $this->showQrModal = true;
        $this->reset('qrMedium', 'qrCodeSvg', 'generatedUrl');
        $this->modal('generate-qr-code')->show();
    }

    public function generateQrCode(): void
    {
        $this->validate([
            'qrMedium' => 'required|string|max:255',
        ]);

        $this->generatedUrl = route('visitor.register', ['medium' => $this->qrMedium]);

        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(600, 4, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(0, 0, 0))),
                new SvgImageBackEnd
            )
        ))->writeString($this->generatedUrl);

        $this->qrCodeSvg = trim(substr($svg, strpos($svg, "\n") + 1));
    }

    public function closeQrModal(): void
    {
        $this->showQrModal = false;
        $this->reset('qrMedium', 'qrCodeSvg', 'generatedUrl');
        $this->modal('generate-qr-code')->close();
    }

    public function exportVisitors()
    {
        $visitors = Visitor::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('phone', 'ilike', "%{$this->search}%")
                        ->orWhere('company_name', 'ilike', "%{$this->search}%")
                        ->orWhere('current_residential_area', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        $filename = 'visitors_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($visitors) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Phone',
                'Age Group',
                'Current Residential Area',
                'Company Name',
                'Interests',
                'Residential Types',
                'Commercial Types',
                'Plotting Types',
                'Weekend Home Types',
                'Planning to Buy',
                'Areas',
                'Tracking Medium',
                'Registered At',
            ]);

            // Add data rows
            foreach ($visitors as $visitor) {
                fputcsv($file, [
                    $visitor->id,
                    $visitor->name,
                    $visitor->phone,
                    $visitor->age_group,
                    $visitor->current_residential_area,
                    $visitor->company_name,
                    is_array($visitor->interests) ? implode(', ', $visitor->interests) : '',
                    is_array($visitor->residential_types) ? implode(', ', $visitor->residential_types) : '',
                    is_array($visitor->commercial_types) ? implode(', ', $visitor->commercial_types) : '',
                    is_array($visitor->plotting_types) ? implode(', ', $visitor->plotting_types) : '',
                    is_array($visitor->weekend_home_types) ? implode(', ', $visitor->weekend_home_types) : '',
                    $visitor->planning_to_buy,
                    is_array($visitor->areas) ? implode(', ', $visitor->areas) : '',
                    $visitor->tracking_medium,
                    $visitor->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    #[Title('Visitors')]
    public function render()
    {
        $visitors = Visitor::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('phone', 'ilike', "%{$this->search}%")
                        ->orWhere('company_name', 'ilike', "%{$this->search}%")
                        ->orWhere('current_residential_area', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return view('livewire.visitors-list', [
            'visitors' => $visitors,
        ]);
    }
}
