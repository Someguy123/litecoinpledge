<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('transactions', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id');
			$table->enum('type', ['withdrawal', 'deposit']);
			$table->string('address', 40);
			$table->string('tx');
			// used to confirm a withdrawal
			$table->string('confirmation_key');
			$table->integer('confirmations');
            $table->decimal('amount', 16,8);
            /*
             * pending:  payment is NOT done. wait for more confirmations,
             *              or in the case of withdrawals, wait for the daemon.
             * complete: payment is done, nothing left to do.
             * confirm: payment needs to be confirmed via the users email
             * hold:     payment was suspicious/large and needs to be confirmed manually
             * reversed: user balance was negative, reversed payments until positive.
             */
			$table->enum('status', ['pending', 'complete', 'hold', 'reversed', 'confirm']);
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('transactions');
    }
}
