<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonthlyTaskGeneratorService;

class GenerateMonthlyTasks extends Command
{
    protected $signature = 'tasks:generate-monthly';

    protected $description = 'Generate monthly tasks for all active plans';

    public function handle(MonthlyTaskGeneratorService $service): int
    {
        $this->info('Generating monthly tasks...');
        $service->generateTasksForNewMonth();
        $this->info('Monthly tasks generation completed.');
        return Command::SUCCESS;
    }
}
