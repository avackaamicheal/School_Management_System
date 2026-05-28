<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\User;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Console\Command;

class CheckSubscriptionExpiry extends Command
{
    protected $signature   = 'subscriptions:check-expiry';
    protected $description = 'Send warnings for expiring subscriptions and lock expired ones';

    public function handle(): void
    {
        // 1. Warn schools expiring in exactly 7 days
        $expiringSoon = School::query()
            ->where('is_subscribed', true)
            ->whereDate('subscription_expires_at', now()->addDays(7)->toDateString())
            ->get();

        foreach ($expiringSoon as $school) {
            $admin = User::where('school_id', $school->id)
                ->role('SchoolAdmin')
                ->first();

            if ($admin) {
                $admin->notify(new SubscriptionExpiringNotification($school));
                $this->info("Warning sent to: {$school->name}");
            }
        }

        // 2. Lock schools past grace period (expired more than 7 days ago)
        School::where('is_subscribed', true)
            ->where('subscription_expires_at', '<', now()->subDays(7))
            ->update(['is_subscribed' => false]);

        $this->info('Subscription check complete.');
    }
}
