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
            // $table->string('sektor')->nullable()->change();
            $table->tinyInteger('subsektor1_a')->nullable(); //pangan padi
            $table->tinyInteger('subsektor1_b')->nullable(); //pangan palawija
            $table->tinyInteger('subsektor2_a')->nullable(); //hortikultura tahunan
            $table->tinyInteger('subsektor2_b')->nullable(); //hortikultura semusim
            $table->tinyInteger('subsektor3_a')->nullable(); //Perkebunan Tahunan
            $table->tinyInteger('subsektor3_b')->nullable(); //Perkebunan Semusim
            $table->tinyInteger('subsektor4_a')->nullable(); //Peternakan Besar
            $table->tinyInteger('subsektor4_b')->nullable(); //Peternakan Sedang
            $table->tinyInteger('subsektor4_c')->nullable(); //Peternakan Unggas
            $table->tinyInteger('subsektor5_a')->nullable(); //Perikanan Budidaya
            $table->tinyInteger('subsektor5_b')->nullable(); //Perikanan Tangkap Umum
            $table->tinyInteger('subsektor5_c')->nullable(); //Perikanan Tangkap Laut
            $table->tinyInteger('subsektor6_a')->nullable(); //Kehutanan Budidaya
            $table->tinyInteger('subsektor6_b')->nullable(); //Kehutanan Tangkap Umum
            $table->tinyInteger('subsektor6_c')->nullable(); //Kehutanan Pemungutan atau Penangkaran
            $table->tinyInteger('subsektor7_a')->nullable(); //Jasa pertanian
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
