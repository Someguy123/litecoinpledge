<?php
namespace App\Console\Commands;

use App\Project;
use Illuminate\Console\Command;
use App\Transaction;
use App\User;

class ProcessTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ltc:transactions {--verbosity=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deal with pending transactions such as Deposits and Withdrawals';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // access bitcoin via IoC
        // includes automatic "mock" for development
        $btc = app('bitcoin');
        $hotwallet = $btc->getinfo()['balance'];
        $this->info('Hotwallet Balance: '. $hotwallet);
        $this->btc = $btc;
        $this->loadTransactions();
    }

    protected function loadTransactions()
    {
        $transactions = $this->btc->listtransactions('*', 100);
        $command = $this;
        $this->info('Processing deposits from Bitcoin. Count: ' . count($transactions));
        foreach($transactions as $t) {
            if($t['category'] !== 'receive') {
                continue;
            }

            try {
                $this->processDeposit($t);
            } catch(\Exception $e) {
                $this->error('There was an error processing transactions: '.$e->getMessage());
            }
        }
        $this->info('Processing withdrawals into Bitcoin');

        // once we've processed deposits, we move on to withdrawals
        if(env('PROCESS_WITHDRAWALS') == true)
            Transaction::where('type', 'withdrawal')
                ->where('status', 'pending')
                ->chunk(20, function($chunk) use($command) {
                    foreach($chunk as $w) {
                        try {
                            $command->processWithdrawal($w);
                        } catch(\Exception $e) {
                            $command->error('There was an error processing this withdrawal: '.$e->getMessage());
                        }
                    }
                }
            );
        else $this->error('Withdrawals are disabled in the config. Set PROCESS_WITHDRAWALS=true in .env');
    }

    /**
     * Takes a withdrawal transaction (Array), and passes it through various functions
     * to confirm, and assign it to the correct entity.
     * @param $w Transaction Array[]
     */
    protected function processWithdrawal($w) {
        $command = $this;
        app('db')->transaction(function() use($w, $command) {
            $user = $w->user;
            $balance = $user->balance;
            if($balance < 0) {
                $command->error('ERROR: User '
                    .$user->username.
                    ' has a negative balance. Reversing current withdrawal of '
                    .$w->amount.' BTC'
                );
                $w->status = 'reversed';
                $w->save();
                $user->increment('balance', $w->amount);
                $user->save();
                return null;
            }
            // if everything is fine, then we send it through Bitcoin and put
            // the TXID into the database

            $btc = app('bitcoin');
            $hotwallet = $btc->getinfo()['balance'];
            $with_fees = bcadd($w->amount, '0.001');
            if($hotwallet < $with_fees) {
				app('mail')->raw("User '$user->username' tried to withdraw $with_fees BTC, available balance: $hotwallet BTC", function ($m) use ($user) {
					$m->from(env('FROM_EMAIL'), env('FROM_NAME'));
					$m->to('support@litecoinpledge.org')->cc('info+lp@someguy123.com');
					$m->subject('Hot Wallet Empty');
				});
                return $command->error('Hot wallet is low, not enough to cover amount '.$with_fees.' - withdrawal ID '.$w->id);
            }
            $command->info("Sending $w->amount to $w->address (user: $user->username)");
            $w->tx = $btc->sendtoaddress($w->address, (float) $w->amount);
            $w->status = 'complete';
            $w->save();
        });

    }

    /**
     * Takes a deposit transaction (Array), and passes it through various functions
     * to confirm, and assign it to the correct entity.
     * @param $t Transaction Array[]
     */
    protected function processDeposit($t)
    {
        $command = $this;
        app('db')->transaction(function () use ($t, $command) {
            $dbt = Transaction::where('tx', $t['txid'])
                ->where('type', 'deposit')->first();

            if ($dbt === null) {
                // does it belong to one of our users?
                $u = User::where('ltc_address', $t['address'])->first();
                if($command->option('verbosity') > 1)
                	$command->info('Processing a new transaction, ID ' . $t['txid']);
                if ($u !== null) {
					if($command->option('verbosity') > 1)
                    	$command->info('User found. Handling TX');
                    $tran = new Transaction();
                    $tran->type = 'deposit';
                    $tran->amount = $t['amount'];
                    $tran->user_id = $u->id;
                    $tran->confirmations = $t['confirmations'];
                    $tran->status = 'pending';
                    $tran->tx = $t['txid'];
                    $tran->address = $t['address'];
                    // tran is saved within this:
                    $command->completeTransaction($t, $tran);
                } else {
                    if(!$command->tryProject($t)) {
                        if ($command->option('verbosity') > 1)
                            $command->info('User not found... Address:' . $t['address'] . 'User data: ' . $u);
                    }
                }
                // if not, we do nothing
                return;
            } else {
				if($this->option('verbosity') > 1)
					$command->info('Processing a '.strtoupper($dbt->status).' transaction, ID ' . $t['txid']);
                if($dbt->status == "pending") {
                    $command->completeTransaction($t, $dbt);
                }
            }
        });
    }

    /**
     * Check if we can complete a transaction (has it got enough confirms),
     * if so, set to complete and increment the users balance
     * @param $t
     * @param $tran
     * @return bool
     */
    function completeTransaction($t, Transaction $tran)
    {
        if ($t['confirmations'] > 0) {
            if($tran->user_id > 0) {
                $u = $tran->user;
                $u->increment('balance', $t['amount']);
                $u->save();
                $this->info("Incremented user '$u->username' balance by $tran->amount");
            } else if ($tran->project_id > 0) {
                $p = $tran->project;
                $p->increment('project_balance', $t['amount']);
                $this->info("Incremented project '$p->id' balance by $tran->amount");
                $p->save();
            } else {
                return false;
            }
            $tran->confirmations = $t['confirmations'];
            $tran->status = 'complete';
        }
        $tran->save();
        return true;
    }

    /**
     * Check if a transaction belongs to a Project, rather than a User
     * @param $t Transaction Array[]
     * @return bool
     */
    protected function tryProject($t)
    {
        $project = Project::where('ltc_address', $t['address'])->first();
        if($project !== null) {
            if ($this->option('verbosity') > 1)
                $this->info('User found. Handling TX');
            $tran = new Transaction();
            $tran->type = 'deposit';
            $tran->amount = $t['amount'];
            $tran->user_id = 0;
            $tran->project_id = $project->id;
            $tran->confirmations = $t['confirmations'];
            $tran->status = 'pending';
            $tran->tx = $t['txid'];
            $tran->address = $t['address'];
            // tran is saved within this:
            return $this->completeTransaction($t, $tran);
        }
        // project not found
        return false;
    }
}