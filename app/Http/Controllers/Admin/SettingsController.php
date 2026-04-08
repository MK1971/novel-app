<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SettingsController extends Controller
{
    public function edit()
    {
        Gate::authorize('admin');

        $adminNotificationEmail = AppSetting::get(AppSetting::KEY_ADMIN_NOTIFICATION_EMAIL, '');

        return view('admin.settings.edit', compact('adminNotificationEmail'));
    }

    public function update(Request $request)
    {
        Gate::authorize('admin');
        $validated = $request->validate([
            'admin_notification_email' => ['nullable', 'string', 'max:255', 'email'],
        ]);

        AppSetting::set(
            AppSetting::KEY_ADMIN_NOTIFICATION_EMAIL,
            $validated['admin_notification_email'] ?? ''
        );

        return back()->with('success', 'Settings saved.');
    }
}
