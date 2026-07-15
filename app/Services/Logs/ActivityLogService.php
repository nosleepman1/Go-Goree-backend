<?php

namespace App\Services\Logs;

use App\Models\ActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    /**
     * Log an administrative action.
     */
    public function log(string $action, ?string $details = null, ?string $userId = null, ?string $userName = null, ?string $ip = null): ActivityLog
    {
        $user = auth()->user();
        
        return ActivityLog::create([
            'user_id' => $userId ?? $user?->id,
            'user_name' => $userName ?? ($user ? $user->prenom . ' ' . $user->nom : 'Système'),
            'action' => $action,
            'details' => $details,
            'ip_address' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * List all activity logs with pagination.
     */
    public function listLogs(): LengthAwarePaginator
    {
        return ActivityLog::orderByDesc('created_at')->paginate(20);
    }
}
