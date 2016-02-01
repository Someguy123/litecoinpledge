<?php

namespace App\Http\Controllers;

use App\Pool;
use App\Transaction;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

const TX_FEE = '0.001';

class WalletController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
    }

    public static function generateAddress($entity, $project = false)
    {
        $id_name = $project ? "project_id" : "user_id";
        // now generate their address
        $count = Pool::whereNull('used')
            ->limit(1)
            ->update([$id_name => $entity->id, 'used' => date('Y-m-d H:i:s', time())]);
        if ($count == 1) {
            $address = Pool::where($id_name, $entity->id)->pluck('address')[0];
            $entity->ltc_address = $address;
            $entity->save();
        }
        return isset($address) ? $address : false;

    }

    public function generate(Request $request)
    {
        $user = $request->user();
        if ($user->ltc_address !== '') {
            return redirect()->back()->withErrors('You already have an address');
        }
        $address = self::generateAddress($user);
        if ($address == false) {
            return redirect()->back()->withErrors("Address pool low. Try again later");
        } else {
            return redirect()->back()->with('status', 'Generated address');
        }
    }

    public function withdraw(Request $request)
    {
        app('db')->transaction(function () use ($request, &$response) {
            $user = $request->user();

            $amount = bcmul('1', $request->input('amount')); // truncate extra decimals
            $u = app('db')->table('users')->where('id', $request->user()->id)->lockForUpdate();
            $balance = $u->first()->balance;
            if($amount == 0) {
                return $response = redirect()->back()->withErrors("You can't withdraw 0");
            }
            if($amount == TX_FEE) {
                return $response = redirect()->back()->withErrors("Amount is too small for TX fee of " . TX_FEE);
            }
            if (bccomp($balance, bcsub($amount, TX_FEE)) >= 0) {
                // change balance atomically
                app('db')->table('users')
                    ->where('id', $user->id)
                    ->decrement('balance', $amount);
                $user->save();
                // We do NOT talk to Litecoin directly as that can introduce race conditions.
                // Store the withdrawal in the DB with 'pending'
                $t = new Transaction;
                $t->user_id = $user->id;
                $t->type = 'withdrawal';
                $t->address = $request->input('address');
                $t->amount = bcsub($amount, TX_FEE); // tx fee
                $t->status = 'confirm';
                $t->confirmation_key = rand_str();
                $t->save();

                Mail::send('emails.confirm_withdraw', ['user' => $user, 'transaction' => $t], function ($m) use ($user) {
                    $m->from(env('FROM_EMAIL'), env('FROM_NAME'));
                    $m->to($user->email, $user->name)->subject('Confirm LitecoinPledge Withdrawal');
                });
                $response = redirect()->back()->with('status', 'Withdrawal was successful. Please check your emails to verify it.');
            } else {
                $response = redirect()->back()->withErrors('Not enough balance to withdraw');
            }
        });
        return $response;
    }

    public function balance(Request $request)
    {
        return $request->user()->balance;
    }

    public function confirm(Request $request, $key)
    {
        $t = Transaction::where('confirmation_key', $key)->firstOrFail();
        if ($t->status == 'confirm') {
            $t->status = 'pending';
            $t->save();
            return "Your withdrawal has been released. You should receive your coins within the next few hours";
        }
        return "Transaction not awaiting confirmation, locked by admin, or already completed.";
    }

    public function cancel(Request $request, $key)
    {
        $t = Transaction::where('confirmation_key', $key)->firstOrFail();
        if ($t->status == 'confirm') {
            $t->status = 'reversed';
            $t->save();
            $t->user->increment('balance', $t->amount);
            $t->user->save();
            return "Your withdrawal has been aborted. You may need to refresh to see your coins.";
        }
        return "Transaction not awaiting confirmation, locked by admin, or already completed.";
    }
}
