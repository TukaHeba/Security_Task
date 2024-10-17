<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Jobs\GenerateDailyTaskReport;
use App\Jobs\GenerateTaskStatusReport;

class ReportController extends Controller
{
    /**
     * Trigger the daily task report generation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDailyTaskReport()
    {
        GenerateDailyTaskReport::dispatch();

        return response()->json(['message' => 'Daily task report generation job dispatched.'], 200);
    }
}
