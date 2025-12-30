<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Visitor;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

use function Laravel\Prompts\progress;

class SendVisitorWhatsAppMessages extends Command
{
    protected $signature = 'visitors:send-whatsapp
                            {--limit= : Limit number of visitors to process}
                            {--visitor= : Send to specific visitor ID}
                            {--phone= : Send to specific phone number}';

    protected $description = 'Send WhatsApp messages with QR codes to registered visitors';

    public function handle(): int
    {
        $query = Visitor::query();

        if ($visitorId = $this->option('visitor')) {
            $query->where('id', $visitorId);
        }

        if ($phone = $this->option('phone')) {
            $query->where('phone', $phone);
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $visitors = $query->get();

        if ($visitors->isEmpty()) {
            $this->error('No visitors found matching the criteria.');

            return self::FAILURE;
        }

        $this->info("Found {$visitors->count()} visitor(s) to process.");
        $this->newLine();

        $qrCodeService = new QrCodeService;
        $success = 0;
        $failed = 0;

        progress(
            label: 'Sending WhatsApp messages',
            steps: $visitors,
            callback: function (Visitor $visitor) use ($qrCodeService, &$success, &$failed) {
                try {
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

                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->error("Failed for {$visitor->name} ({$visitor->phone}): {$e->getMessage()}");
                }
            }
        );

        $this->newLine();
        $this->info("✓ Successfully queued: {$success}");

        if ($failed > 0) {
            $this->error("✗ Failed: {$failed}");
        }

        $this->newLine();
        $this->comment('Messages are queued. Run "php artisan queue:work" to process them.');
        $this->comment('Monitor logs: tail -f storage/logs/whatsapp.log');

        return self::SUCCESS;
    }
}
