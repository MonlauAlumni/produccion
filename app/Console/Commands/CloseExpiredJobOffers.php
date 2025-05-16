<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobOffer;

class CloseExpiredJobOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-expired-job-offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      JobOffer::where('status', 'active')
        ->whereDate('deadline', '<', now())
        ->update(['status' => 'closed']);

    $this->info('Expired job offers have been closed.');
    }
}
