<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;

class FillAddressPool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ltc:fillpool {--amtaddr=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill address pool if it runs lows';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $db = app('db');
        $unused_count = $db->select('SELECT count(*) - count(used) as unused FROM pool')[0]->unused;
        $this->info('Counted '.$unused_count.' unused addresses');
        $amount = $this->option('amtaddr');
        if($unused_count < 50) {
            $this->info('Filling pool');
            $this->popAddress($amount);
        } else {
            $this->info('Not filling pool');
        }
    }
    protected function popAddress($times) {
        $db = app('db');
        $btc = app('bitcoin');

        for($i=1; $i<=$times; $i++) {
            $db->insert('INSERT INTO pool (address) VALUES (?)', [$btc->getnewaddress()]);
        }
    }

}