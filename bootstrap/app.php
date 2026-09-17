<?php

use App\Http\Middleware\CheckSchoolApproval;
use App\Http\Middleware\SetTenantSchool;
use App\Notifications\ApplicationErrorNotification;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Illuminate\Support\Facades\Notification as NotificationFacade;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->web(append: [
            CheckSchoolApproval::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'active' => SetTenantSchool::class,
            'school.approved' => CheckSchoolApproval::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            // Only notify in production
            if (!app()->environment('production')) {
                return;
            }

            try {
                $email = env('BACKUP_NOTIFICATION_EMAIL');

                if ($email) {
                    NotificationFacade::route('mail', $email)
                        ->notify(new ApplicationErrorNotification($e));
                }
            } catch (\Throwable $notifyException) {
                report($notifyException);
            }
        });
    })->create();
