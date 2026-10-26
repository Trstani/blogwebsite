<?php

namespace App\Console\Commands;

use App\Models\OtpCode;
use App\Models\PendingRegistration;
use Illuminate\Console\Command;

class CleanupExpiredOtpCommand extends Command
{
    protected $signature = 'cleanup:expired-otp';

    protected $description = 'Delete expired pending registrations and OTP codes';

    public function handle(): int
    {
        $now = now();

        $deletedRegistrations = PendingRegistration::query()
            ->where('expires_at', '<', $now)
            ->delete();

        $deletedOtps = OtpCode::query()
            ->where('expires_at', '<', $now)
            ->delete();

        $this->info('Expired data cleanup completed.');
        $this->line("Pending registrations deleted: {$deletedRegistrations}");
        $this->line("OTP codes deleted: {$deletedOtps}");

        return self::SUCCESS;
    }
}
