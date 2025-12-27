<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckTaskExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-task-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check if tasks haven't been completed for a long time";

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        echo "Checking task expiration...\n";
        $tasks = Task::query()->where('status', 'pending')->get();
        if ($tasks !== null) {
            foreach ($tasks as $task) {
                if (time() >= Carbon::parse($task->expires_at)->timestamp) {
                    $task->status = 'failed';
                    $task->save();
                }
            }
        }
        echo "Finished checking task expiration...\n";
    }
}
