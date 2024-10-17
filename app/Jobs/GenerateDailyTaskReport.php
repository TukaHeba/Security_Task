<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use App\Models\TaskStatusUpdate;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateDailyTaskReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $taskId
     * @return void
     */
    public function __construct($taskId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * Fetch all status updates for today and prepare the data
     * Then log or save the report
     * @return void
     */
    public function handle()
    {
        $todayUpdates = TaskStatusUpdate::whereDate('created_at', now())->get();

        $reportData = [
            'date' => now()->toDateString(),
            'total_updates' => $todayUpdates->count(),
            'updates' => $todayUpdates->map(function ($update) {
                return [
                    'task_id' => $update->task_id,
                    'previous_status' => $update->previous_status,
                    'new_status' => $update->new_status,
                    'changed_by' => $update->user_id,
                    'changed_at' => $update->created_at->toDateTimeString(),
                ];
            }),
        ];

        Log::info('Daily Task Status Report', $reportData);
        Storage::put('reports/daily_task_status_report_' . now()->toDateString() . '.json', json_encode($reportData));
    }
}
