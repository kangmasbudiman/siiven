<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTolakToDetailUsulanPembeliansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_usulan_pembelians', function (Blueprint $table) {
            $table->boolean('is_ditolak')->default(false)->after('harga_satuan');
            $table->text('alasan_tolak')->nullable()->after('is_ditolak');
        });
    }

    public function down()
    {
        Schema::table('detail_usulan_pembelians', function (Blueprint $table) {
            $table->dropColumn(['is_ditolak', 'alasan_tolak']);
        });
    }
}
