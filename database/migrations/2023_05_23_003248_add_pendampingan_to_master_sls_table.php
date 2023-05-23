<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendampinganToMasterSlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_sls', function (Blueprint $table) {
            $table->text('pendampingan_pml')->nullable();
            $table->text('pendampingan_koseka')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_sls', function (Blueprint $table) {
            //
        });
    }
}
