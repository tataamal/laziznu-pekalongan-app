<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\KoinNuTransactionRepository;

class PruneCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:prune-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus cache entries yang sudah expired';

    /**
     * Execute the console command.
     */
    public function handle(KoinNuTransactionRepository $repo): int
    {
        $deleted = $repo->pruneExpiredCache();
        $this->info("Berhasil menghapus {$deleted} cache entries.");
        return self::SUCCESS;
    }
}
