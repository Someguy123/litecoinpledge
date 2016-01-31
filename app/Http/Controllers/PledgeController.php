<?php

namespace App\Http\Controllers;

use App\Pledge;
use App\Project;
use App\UserPledge;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PledgeController extends Controller
{
    function createPledge(Requests\PledgeRequest $r, Project $project) {
        {
            $type = $r->input('type');
            $amount = $r->input('amount');
            $user = $r->user();
            $_this = $this;

            if ($type == "monthly") {
                $existing = UserPledge::where('user_id', $user->id)
                    ->where('project_id', $project->id)
                    ->get()->first();
                if ($existing !== null)
                    return redirect()
                        ->back()
                        ->withErrors('You already have a recurring pledge for this project. Change it from "My Account".');
                try {
                    app('db')->transaction(function () use ($amount, $user, $type, $project, $_this) {

                        $_this->updateBalances($user, $amount, $project);
                        // First we create the "contract" of them paying LTC each month
                        $upledge = new UserPledge();
                        $upledge->amount = $amount;
                        $upledge->frequency = $type;
                        $upledge->project_id = $project->id;
                        $upledge->user_id = $user->id;
                        $upledge->last_pledge = \Carbon\Carbon::now();
                        $upledge->save();
                        // Then we write this months payment as a "pledge"
                        $_this->storePledge($amount, $type, $project, $user);

                    });
                    return redirect('/projects/' . $project->id)->with('status', 'Successfully created a monthly pledge. First months payment has been taken from your balance.');

                } catch(BalanceException $e) {
                    return redirect()->back()->withErrors('Not enough balance for this pledge.');
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors('There was an error processing your pledge. Your balance should not be affected.');
                }
            }
            // if it's only one time, we simply update the balance then store a single pledge
            if($type == "once") {
                try {
                    app('db')->transaction(function () use ($amount, $user, $type, $project, $_this) {
                        $_this->updateBalances($user, $amount, $project);
                        $_this->storePledge($amount, $type, $project, $user);
                    });
                    return redirect('/projects/'.$project->id)->with('status', 'Successfully sent a one time pledge. The payment has been taken from your balance.');
                } catch (BalanceException $e) {
                    return redirect()->back()->withErrors('Not enough balance for this pledge.');
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors('There was an error processing your pledge. Your balance should not be affected.');
                }
            }

        }
    }
    function storePledge($amount, $type, Project $project, $user) {
        $pledge = new Pledge();
        $pledge->amount = $amount;
        $pledge->user_id = $user->id;
        $pledge->type = $type;
        $pledge->project_id = $project->id;
        $pledge->save();
    }

    /**
     * @param $user
     * @param $amount
     * @param $project
     */
    function updateBalances($user, $amount, $project)
    {
        // We lock the row using 'FOR UPDATE', to prevent race conditions
        $db_user = app('db')->select('SELECT id, balance FROM users WHERE id = ? FOR UPDATE', [$user->id])[0];
        if (bccomp($db_user->balance, $amount, 8) < 0) {
            throw new BalanceException();
        }
        app('db')->table('users')->where('id', $user->id)->decrement('balance', $amount);
        app('db')->update('UPDATE projects
                            SET project_balance = project_balance + ?,
                                total_pledged = total_pledged + ?,
                                updated_at = NOW()
                            WHERE projects.id = ?', [$amount, $amount, $project->id]);
    }

}

class BalanceException extends \Exception {}
