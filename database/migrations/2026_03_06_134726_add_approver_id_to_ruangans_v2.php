<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApproverIdToRuangansV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->string('approver_id', 6)->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropColumn('approver_id');
        });
    }
}
