<?php

namespace App\Http\Controllers;

use App\Models\CollaborationReminder;
use Illuminate\Http\Request;

class ReminderSettingsController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'reminders_enabled' => ['required', 'boolean'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'reminder_days_before' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);

        $u = $request->user();

        $u->reminders_enabled = (bool) $data['reminders_enabled'];

        if (array_key_exists('reminder_time', $data) && $data['reminder_time'] !== null) {
            $u->reminder_time = $data['reminder_time'];
        } elseif (!$u->reminder_time) {
            $u->reminder_time = '08:30';
        }

        if (array_key_exists('reminder_days_before', $data) && $data['reminder_days_before'] !== null) {
            $u->reminder_days_before = (int) $data['reminder_days_before'];
        } elseif ($u->reminder_days_before === null) {
            $u->reminder_days_before = 0;
        }

        $u->save();

        // ako ugasi reminders -> cancel pending
        if (!$u->reminders_enabled) {
            CollaborationReminder::where('user_id', $u->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        return response()->json(['ok' => true]);
    }
}
