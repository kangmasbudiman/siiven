<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalLevelToUserTable extends Migration
{
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->tinyInteger('approval_level')->nullable()->default(null)->after('hakAkses')
                ->comment('NULL=Ka.Unit, 1=Pemeriksa, 2=Konfirmator, 3=Kabag, 4=Direktur');
        });
    }

    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('approval_level');
        });
    }
}
