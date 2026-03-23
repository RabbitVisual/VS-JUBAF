<?php

namespace Modules\Notifications\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Notifications\App\Models\NotificationAuditLog;

class NotificationDashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $from = now()->subDays($days);

        $sent = NotificationAuditLog::where('created_at', '>=', $from)->where('status', 'sent')->count();
        $failed = NotificationAuditLog::where('created_at', '>=', $from)->where('status', 'failed')->count();
        $opened = NotificationAuditLog::where('created_at', '>=', $from)->where('status', 'opened')->count();
        $total = $sent + $failed;
        $successRate = $total > 0 ? round(100 * $sent / $total, 1) : 0;
        $openRate = $sent > 0 ? round(100 * $opened / $sent, 1) : 0;

        $byChannel = NotificationAuditLog::where('created_at', '>=', $from)
            ->select('channel', DB::raw('count(*) as total'))
            ->groupBy('channel')
            ->pluck('total', 'channel');

        $dailySent = NotificationAuditLog::where('created_at', '>=', $from)
            ->where('status', 'sent')
            ->select(DB::raw('date(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dailyFailed = NotificationAuditLog::where('created_at', '>=', $from)
            ->where('status', 'failed')
            ->select(DB::raw('date(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        return view('notifications::admin.control.dashboard', [
            'days' => $days,
            'sent' => $sent,
            'failed' => $failed,
            'opened' => $opened,
            'successRate' => $successRate,
            'openRate' => $openRate,
            'byChannel' => $byChannel,
            'dailySent' => $dailySent,
            'dailyFailed' => $dailyFailed,
        ]);
    }
}
