<?php

namespace App\Observers;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Visitor;
use App\Services\QrCodeService;

class VisitorObserver
{
    /**
     * Handle the Visitor "created" event.
     */
    public function created(Visitor $visitor): void
    {
        $qrCodeService = new QrCodeService;
        $url = route('visitor.show', $visitor);
        $filename = 'visitor-'.$visitor->uuid;
        $imageUrl = $qrCodeService->withSize(512, 2)->saveWhatsAppQrImage($url, $filename);

        SendWhatsAppMessage::dispatch(
            name: $visitor->name,
            phoneNumber: $visitor->phone,
            templateName: 'user_registration_1_copy',
            data: [
                $visitor->name,
                'GLAM SURAT – Property Show 2026',
                '9, 10, 11 January 2026',
                'Vanita Vishram Ground, Surat',
            ],
            imageUrl: $imageUrl,
            buttonValue: 'https://property-show.credai-surat.com/',
            visitorId: $visitor->id
        );
    }

    /**
     * Handle the Visitor "updated" event.
     */
    public function updated(Visitor $visitor): void
    {
        //
    }

    /**
     * Handle the Visitor "deleted" event.
     */
    public function deleted(Visitor $visitor): void
    {
        //
    }

    /**
     * Handle the Visitor "restored" event.
     */
    public function restored(Visitor $visitor): void
    {
        //
    }

    /**
     * Handle the Visitor "force deleted" event.
     */
    public function forceDeleted(Visitor $visitor): void
    {
        //
    }
}
