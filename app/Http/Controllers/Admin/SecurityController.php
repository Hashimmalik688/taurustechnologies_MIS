<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    /**
     * Receive a screenshot-suspect or devtools alert from the client.
     * Logs to AuditLog and notifies all Super Admins.
     */
    public function reportSuspect(Request $request)
    {
        $user    = auth()->user();
        $trigger = $request->input('trigger', 'unknown');
        $url     = $request->input('url', '');

        $triggerLabels = [
            'printscreen_key'   => 'PrintScreen key pressed (screenshot attempted)',
            'devtools_opened'   => 'Browser DevTools opened',
            'ctrl_p'            => 'Print initiated (Ctrl+P / beforeprint)',
            'copy_attempt'      => 'Attempted to copy data',
            'cut_attempt'       => 'Attempted to cut data',
            'rightclick_attempt'=> 'Right-click attempted on page',
            'drag_attempt'      => 'Attempted to drag page content',
            'f12_key'           => 'F12 key pressed (DevTools shortcut)',
            'devtools_shortcut' => 'Ctrl+Shift+I/J/C pressed (DevTools shortcut)',
            'view_source'       => 'Ctrl+U pressed (View Source)',
            'save_page'         => 'Ctrl+S pressed (Save Page)',
            'ctrl_c'            => 'Ctrl+C pressed (Copy shortcut)',
            'ctrl_x'            => 'Ctrl+X pressed (Cut shortcut)',
            'ctrl_a'            => 'Ctrl+A pressed (Select All on protected content)',
        ];

        $label = $triggerLabels[$trigger] ?? "Security event: {$trigger}";

        // ── Audit Log ─────────────────────────────────────────────────
        AuditLog::logAction(
            'security_suspect',
            $user,
            null,
            null,
            [
                'trigger'    => $trigger,
                'page_url'   => $url,
                'user_id'    => $user?->id,
                'user_name'  => $user?->name,
                'user_email' => $user?->email,
            ],
            $label
        );

        // ── Notify all Super Admins ────────────────────────────────────
        $superAdmins = User::role(Roles::SUPER_ADMIN)->get();

        foreach ($superAdmins as $admin) {
            Notification::createForUser(
                $admin->id,
                '🚨 Security Alert: ' . $label,
                sprintf(
                    '%s (%s) triggered a security event — %s — on page: %s',
                    $user?->name ?? 'Unknown user',
                    $user?->email ?? 'N/A',
                    $label,
                    parse_url($url, PHP_URL_PATH) ?? $url
                ),
                [
                    'type'         => 'warning',
                    'icon'         => 'bx-shield-x',
                    'color'        => 'danger',
                    'is_important' => true,
                ]
            );
        }

        return response()->json(['ok' => true]);
    }
}
