<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneDownloadLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'downloads:prune-logs {--days=90}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
{
    $days = (int) ($this->option('days') ?? 90);

    \DB::table('download_logs')
        ->where('created_at', '<', now()->subDays($days))
        ->delete();

    $this->info("Pruned download_logs older than {$days} days.");
    return 0;
}

}
