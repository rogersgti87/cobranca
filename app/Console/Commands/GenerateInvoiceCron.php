<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class GenerateInvoiceCron extends Command
{

  protected $signature = 'generateinvoice:cron';

  protected $description = 'Gerar Faturas';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {



  }

}
