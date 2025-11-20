<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GlobalQueueWork extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'queue:work-global 
                          {--once : Only process the next job on the queue}
                          {--stop-when-empty : Stop when the queue is empty}
                          {--max-jobs= : The number of jobs to process before stopping}
                          {--max-time= : The maximum number of seconds to run}
                          {--sleep=3 : Number of seconds to sleep when no job is available}
                          {--timeout=60 : The number of seconds a child process can run}
                          {--tries=1 : Number of times to attempt a job before logging it failed}';

    /**
     * The console command description.
     */
    protected $description = 'Process queue jobs for the global database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $globalConnection = config('database.global');

        if (!$globalConnection) {
            $this->error('Global database connection not configured');
            return 1;
        }

        $this->info("Starting queue worker for global database connection: {$globalConnection}");

        try {
            $options = [
                '--sleep' => $this->option('sleep'),
                '--timeout' => $this->option('timeout'),
                '--tries' => $this->option('tries'),
            ];

            if ($this->option('once')) {
                $options['--once'] = true;
            }

            if ($this->option('stop-when-empty')) {
                $options['--stop-when-empty'] = true;
            }

            if ($this->option('max-jobs')) {
                $options['--max-jobs'] = $this->option('max-jobs');
            }

            if ($this->option('max-time')) {
                $options['--max-time'] = $this->option('max-time');
            }

            // Process the queue - connection name is the first argument
            $exitCode = Artisan::call('queue:work', [$globalConnection] + $options);

            if ($exitCode === 0) {
                $this->info('✓ Global queue worker completed successfully');
            }

            return $exitCode;

        } catch (\Exception $e) {
            $this->error("Queue worker failed: " . $e->getMessage());
            return 1;
        }
    }
}
