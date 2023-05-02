<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRincianSubsektorToRutaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ruta', function (Blueprint $table) {
            $table->string('sektor')->nullable()->change();
            $table->integer('jumlah_art')->nullable();
            $table->integer('jumlah_unit_usaha')->nullable();
            $table->integer('jml_308_sawah')->nullable();
            $table->integer('jml_308_bukan_sawah')->nullable();
            $table->integer('jml_308_rumput_sementara')->nullable();
            $table->integer('jml_308_rumput_permanen')->nullable();
            $table->integer('jml_308_belum_tanam')->nullable();
            $table->integer('jml_308_ternak_bangunan_lain')->nullable();
            $table->integer('jml_308_kehutanan')->nullable();
            $table->integer('jml_308_budidaya')->nullable();
            $table->integer('jml_308_lahan_lainnya')->nullable();
            $table->string('daftar_komoditas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ruta', function (Blueprint $table) {
            //
        });
    }
}
