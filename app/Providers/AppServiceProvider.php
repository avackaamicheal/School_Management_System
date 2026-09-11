<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\GradeRecord;
use App\Models\Invoice;
use App\Models\MessageThread;
use App\Models\Payment;
use App\Models\School;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\GradeRecordPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\MessagePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\SchoolPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(School::class, SchoolPolicy::class);
        Gate::policy(GradeRecord::class, GradeRecordPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(MessageThread::class, MessagePolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Blade helper for active links in side bar in the header
        // Usage: @activeRoute('grades.*')
        Blade::directive('activeRoute', function ($expression) {
            return "<?php echo request()->routeIs({$expression}) ? 'active' : ''; ?>";
        });


        // second blade helper for tree views in side bar
        // Add a second directive to AppServiceProvider
        Blade::directive('menuOpen', function ($expression) {
            return "<?php echo request()->routeIs({$expression}) ? 'menu-open' : ''; ?>";
        });

        Blade::directive('route', function ($expression) {
            return "<?php echo resolveRoute({$expression}); ?>";
        });
    }



}
