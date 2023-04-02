<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterSlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_sls', function (Blueprint $table) {
            $table->id();

            $table->string("kode_prov", 2);
            $table->string("kode_kab", 2);
            $table->string("kode_kec", 3);
            $table->string("kode_desa", 3);

            $table->string("id_sls", 4);
            $table->string("id_sub_sls", 4);
            $table->string("nama_sls");

            $table->integer("sls_op");
            
            $table->integer("jenis_sls");
            
            $table->integer("jml_art_tani")->default(0);
            $table->integer("jml_keluarga_tani")->default(0);
            $table->integer("sektor1")->default(0);
            $table->integer("sektor2")->default(0);
            $table->integer("sektor3")->default(0);
            $table->integer("sektor4")->default(0);
            $table->integer("sektor5")->default(0);
            $table->integer("sektor6")->default(0);

            $table->integer("jml_keluarga_tani_st2023")->default(0);
            $table->integer("jml_nr")->default(0);

            $table->integer("jml_dok_ke_pml")->default(0);
            $table->integer("jml_dok_ke_koseka")->default(0);
            $table->integer("jml_dok_ke_bps")->default(0);

            $table->integer("status_selesai_pcl")->default(0);
            
            $table->string("kode_pcl")->nullable();
            $table->string("kode_pml")->nullable();
            $table->string("kode_koseka")->nullable();
            
            $table->integer("status_sls")->default(0);
            $table->integer("created_by");
            $table->integer("updated_by");
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
        Schema::dropIfExists('master_sls');
    }
}
