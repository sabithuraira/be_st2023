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
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('start_latitude', 10, 8);
            $table->decimal('end_latitude', 10, 8);
            $table->decimal('start_longitude', 11, 8);
            $table->decimal('end_longitude', 11, 8);
            $table->integer('created_by');
            $table->integer('updated_by');
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
