<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class InvoiceJobCron extends Command
{

  protected $signature = 'invoicejob:cron';

  protected $description = 'Executar jobs';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {

    $jobs = DB::table('jobs')->get();

    foreach($jobs as $job){
        \Artisan::call('queue:work --queue='.$job->queue.' --once');
    }

  }

}
