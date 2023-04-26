<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRutaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ruta', function (Blueprint $table) {
            $table->id();
            $table->string('kode_prov', 2);
            $table->string('kode_kab', 2);
            $table->string('kode_kec', 3);
            $table->string('kode_desa', 3);
            $table->string('id_sls', 4);
            $table->string('id_sub_sls', 4);
            $table->integer('nurt');
            $table->string('kepala_ruta')->nullable();
            $table->integer('sektor')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('ruta');
    }
}
