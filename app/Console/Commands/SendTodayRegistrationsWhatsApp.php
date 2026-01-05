<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Partner;
use App\Models\Visitor;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

use function Laravel\Prompts\progress;

class SendTodayRegistrationsWhatsApp extends Command
{
    protected $signature = 'registrations:send-whatsapp-today
                            {--type= : Type to send (visitors, partners, or both)}
                            {--dry-run : Show who would receive messages without sending}';

    protected $description = 'Send WhatsApp messages to partners and visitors who registered today after 9 AM';

    public function handle(): int
    {
        $type = $this->option('type') ?? 'both';
        $dryRun = $this->option('dry-run');

        $today9am = now()->setTime(9, 0, 0);

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No messages will be sent');
            $this->newLine();
        }

        $totalSuccess = 0;
        $totalFailed = 0;

        if (in_array($type, ['visitors', 'both'])) {
            $this->info('Processing visitors...');
            [$success, $failed] = $this->processVisitors($today9am, $dryRun);
            $totalSuccess += $success;
            $totalFailed += $failed;
        }

        if (in_array($type, ['partners', 'both'])) {
            $this->info('Processing partners...');
            [$success, $failed] = $this->processPartners($today9am, $dryRun);
            $totalSuccess += $success;
            $totalFailed += $failed;
        }

        $this->newLine();
        $this->info("✓ Successfully queued: {$totalSuccess}");

        if ($totalFailed > 0) {
            $this->error("✗ Failed: {$totalFailed}");
        }

        if (! $dryRun) {
            $this->newLine();
            $this->comment('Messages are queued. Run "php artisan queue:work" to process them.');
            $this->comment('Monitor logs: tail -f storage/logs/whatsapp.log');
        }

        return self::SUCCESS;
    }

    protected function processVisitors(\DateTime $cutoffTime, bool $dryRun): array
    {
        $visitors = Visitor::query()
            ->where('created_at', '>=', $cutoffTime)
            ->get();

        if ($visitors->isEmpty()) {
            $this->warn('No visitors found registered after 9 AM today.');

            return [0, 0];
        }

        $this->info("Found {$visitors->count()} visitor(s) to process.");

        if ($dryRun) {
            $this->table(
                ['ID', 'Name', 'Phone', 'Registered At'],
                $visitors->map(fn ($v) => [
                    $v->id,
                    $v->name,
                    $v->phone,
                    $v->created_at->format('Y-m-d H:i:s'),
                ])
            );

            return [$visitors->count(), 0];
        }

        $qrCodeService = new QrCodeService;
        $success = 0;
        $failed = 0;

        progress(
            label: 'Sending visitor WhatsApp messages',
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

        return [$success, $failed];
    }

    protected function processPartners(\DateTime $cutoffTime, bool $dryRun): array
    {
        $partners = Partner::query()
            ->where('created_at', '>=', $cutoffTime)
            ->get();

        if ($partners->isEmpty()) {
            $this->warn('No partners found registered after 9 AM today.');

            return [0, 0];
        }

        $this->info("Found {$partners->count()} partner(s) to process.");

        if ($dryRun) {
            $this->table(
                ['ID', 'Name', 'Firm', 'Phone', 'Registered At'],
                $partners->map(fn ($p) => [
                    $p->id,
                    $p->first_name.' '.$p->last_name,
                    $p->firm_name,
                    $p->phone,
                    $p->created_at->format('Y-m-d H:i:s'),
                ])
            );

            return [$partners->count(), 0];
        }

        $qrCodeService = new QrCodeService;
        $success = 0;
        $failed = 0;

        progress(
            label: 'Sending partner WhatsApp messages',
            steps: $partners,
            callback: function (Partner $partner) use ($qrCodeService, &$success, &$failed) {
                try {
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
                        buttonValue: 'https://property-show.credai-surat.com/'
                    );

                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->error("Failed for {$partnerName} ({$partner->phone}): {$e->getMessage()}");
                }
            }
        );

        return [$success, $failed];
    }
}
