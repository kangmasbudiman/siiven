<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailIdToLampiranUsulanPembeliansTable extends Migration
{
    public function up()
    {
        Schema::table('lampiran_usulan_pembelians', function (Blueprint $table) {
            // nullable: null = lampiran umum (per usulan), isi = lampiran per item
            $table->unsignedBigInteger('detail_usulan_pembelian_id')->nullable()->after('usulan_pembelian_id');

            $table->foreign('detail_usulan_pembelian_id')
                  ->references('id')
                  ->on('detail_usulan_pembelians')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('lampiran_usulan_pembelians', function (Blueprint $table) {
            $table->dropForeign(['detail_usulan_pembelian_id']);
            $table->dropColumn('detail_usulan_pembelian_id');
        });
    }
}
