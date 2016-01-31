<?php

namespace App\Console\Commands;

use App\UserPledge;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PledgeSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:pledge_schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Find users who haven't sent their recent pledges";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // for now, we only support monthly
        $pledges = UserPledge::where('frequency', 'monthly')
                             ->where('last_pledge', '<', Carbon::now()->subDay(31))
                             ->get();
        foreach($pledges as $p) {
            // if we never sent an email before, the default is yesterday
            if($p->last_email->year == -1) {
                $p->last_email = Carbon::now()->subDay();
                $p->save();
            }
            // only send emails every 3 days
            if($p->last_email->gte(Carbon::now()->subDays(3))) {
                return;
            }
            $user = $p->user;
            // 7 days ago, we remove the pledge
            if ($p->last_pledge->lte(Carbon::now()->subDays(38))) {
                Mail::send('emails.next_pledge_7', ['user' => $p->user, 'pledge' => $p], function ($m) use ($user) {
                    $m->from(env('FROM_EMAIL'), env('FROM_NAME'));
                    $m->to($user->email, $user->name)->subject("INACTIVITY - We've removed your pledge");
                });
                $p->delete();
                return;
            }
            // 4 days ago, another reminder
            if ($p->last_pledge->lte(Carbon::now()->subDays(35))) {
                Mail::send('emails.next_pledge_4', ['user' => $p->user, 'pledge' => $p], function ($m) use ($user) {
                    $m->from(env('FROM_EMAIL'), env('FROM_NAME'));
                    $m->to($user->email, $user->name)->subject("You still haven't sent your PLEDGE");
                });
                $p->last_email = Carbon::now();
                $p->save();
                return;
            }
            // last email sent more than a day ago
            if($p->last_pledge->lte(Carbon::now()->subDays(32))) {
                Mail::send('emails.next_pledge_1', ['user' => $p->user, 'pledge' => $p], function ($m) use ($user) {
                    $m->from(env('FROM_EMAIL'), env('FROM_NAME'));
                    $m->to($user->email, $user->name)->subject("It's time for your next PLEDGE");
                });
                $p->last_email = Carbon::now();
                $p->save();
                return;
            }
        }
    }
}
