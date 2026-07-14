<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    public function index(School $school)
    {
        $user = Auth::user();
        $types = NotificationPreference::TYPES;

        $preferences = NotificationPreference::where('user_id', $user->id)
            ->get()
            ->keyBy('notification_type');

        return view('notifications.preferences', compact('types', 'preferences'));
    }

    public function update(Request $request, School $school)
    {
        $user = Auth::user();
        $types = array_keys(NotificationPreference::TYPES);

        foreach ($types as $type) {
            $isCritical = in_array($type, NotificationPreference::CRITICAL);

            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $type,
                ],
                [
                    'email_enabled' => $isCritical
                        ? (bool) $request->input("email_{$type}", false)
                        : false,
                    'in_app_enabled' => (bool) $request->input("in_app_{$type}", false),
                ]
            );
        }

        return back()->with('success', 'Notification preferences saved.');
    }
}
