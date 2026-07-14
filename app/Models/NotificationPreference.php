<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'in_app_enabled',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
    ];

    // Notification type constants
    const TYPES = [
        'subscription_activated' => 'Subscription Activated',
        'subscription_expiring' => 'Subscription Expiring',
        'school_rejected' => 'School Rejected',
        'school_approved' => 'School Approved',
        'new_school_registration' => 'New School Registration',
        'invoice_generated' => 'Invoice Generated',
        'payment_received' => 'Payment Received',
        'absent_alert' => 'Absent Alert',
        'grade_published' => 'Grade Published',
        'student_admitted' => 'Student Admitted',
        'teacher_added' => 'Teacher Added',
        'new_assignment' => 'New Assignment',
        'announcement_posted' => 'Announcement Posted',
    ];

    // Critical = both email and in-app
    const CRITICAL = [
        'subscription_activated',
        'subscription_expiring',
        'school_rejected',
        'school_approved',
        'new_school_registration',
        'invoice_generated',
        'payment_received',
        'absent_alert',
        'grade_published',
    ];
}
