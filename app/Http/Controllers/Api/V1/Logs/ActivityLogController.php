<?php

namespace App\Http\Controllers\Api\V1\Logs;

use App\Http\Controllers\Controller;
use App\Services\Logs\ActivityLogService;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    /**
     * Get system activity logs.
     */
    public function index()
    {
        return response()->json($this->activityLogService->listLogs());
    }
}
