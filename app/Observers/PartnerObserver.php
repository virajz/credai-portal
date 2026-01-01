<?php

namespace App\Observers;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Partner;
use App\Services\QrCodeService;

class PartnerObserver
{
    /**
     * Handle the Partner "created" event.
     */
    public function created(Partner $partner): void
    {
        $qrCodeService = new QrCodeService;
        $url = route('partner.show', $partner);
        $filename = 'partner-'.$partner->uuid;
        $imageUrl = $qrCodeService->withSize(512, 2)->saveWhatsAppQrImage($url, $filename);

        $partnerName = $partner->first_name.' '.$partner->last_name;

        SendWhatsAppMessage::dispatch(
            name: $partnerName,
            phoneNumber: $partner->phone,
            templateName: 'user_registration_1_copy',
            data: [
                $partnerName,
                'GLAM SURAT – Property Show 2026',
                '9, 10, 11 January 2026',
                'Vanita Vishram Ground, Surat',
            ],
            imageUrl: $imageUrl,
            buttonValue: 'https://property-show.credai-surat.com/',
        );
    }

    /**
     * Handle the Partner "updated" event.
     */
    public function updated(Partner $partner): void
    {
        //
    }

    /**
     * Handle the Partner "deleted" event.
     */
    public function deleted(Partner $partner): void
    {
        //
    }

    /**
     * Handle the Partner "restored" event.
     */
    public function restored(Partner $partner): void
    {
        //
    }

    /**
     * Handle the Partner "force deleted" event.
     */
    public function forceDeleted(Partner $partner): void
    {
        //
    }
}
