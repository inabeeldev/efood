<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToBranchWithdrawRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_withdraw_requests', function (Blueprint $table) {
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_withdraw_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
