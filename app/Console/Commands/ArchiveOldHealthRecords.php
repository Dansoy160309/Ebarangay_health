<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HealthRecord;
use Carbon\Carbon;

class ArchiveOldHealthRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:archive-old-records {--years=5 : The number of years to consider a record outdated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically archive health records older than a specified number of years.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $years = $this->option('years');
        $cutoffDate = Carbon::now()->subYears($years);

        $this->info("Archiving health records older than {$years} years (created before {$cutoffDate->toDateString()})...");

        $count = HealthRecord::where('created_at', '<', $cutoffDate)
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived']);

        $this->info("Successfully archived {$count} records.");

        return 0;
    }
}
