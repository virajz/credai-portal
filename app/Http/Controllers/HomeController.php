<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Exhibitor;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'mainSponsors' => $this->getMainSponsors(),
            'associatePartners' => $this->getAssociatePartners(),
            'coPartners' => $this->getCoPartners(),
            'exhibitors' => $this->getOtherExhibitors(),
        ]);
    }

    private function getMainSponsors()
    {
        return Company::with(['exhibitor', 'exhibitor.projects'])
            ->where('stall_type', 'ILIKE', 'Powered By Sponsor%')
            ->where('category', 'Builders')
            ->orderBy('stall_number', 'asc')
            ->limit(3)
            ->get();
    }

    private function getAssociatePartners()
    {
        return Company::with(['exhibitor', 'exhibitor.projects'])
            ->where('stall_type', 'ILIKE', 'Associate%')
            ->where('category', 'Builders')
            ->orderBy('stall_number', 'asc')
            ->get();
    }

    private function getCoPartners()
    {
        return Company::with(['exhibitor', 'exhibitor.projects'])
            ->where('stall_type', 'ILIKE', 'Co Sponsor%')
            ->where('category', 'Builders')
            ->orderBy('stall_number', 'asc')
            ->get();
    }

    private function getOtherExhibitors()
    {
        return Exhibitor::with(['company', 'projects'])
            ->whereHas('company', function ($query) {
                $query->where('category', 'Builders')
                    ->where(function ($q) {
                        $q->where('stall_type', 'NOT ILIKE', 'Powered By Sponsor%')
                            ->where('stall_type', 'NOT ILIKE', 'Associate%')
                            ->where('stall_type', 'NOT ILIKE', 'Co Sponsor%')
                            ->orWhereNull('stall_type');
                    });
            })
            ->join('companies', 'exhibitors.company_id', '=', 'companies.id')
            ->orderBy('companies.company_name', 'asc')
            ->select('exhibitors.*')
            ->get();
    }
}
