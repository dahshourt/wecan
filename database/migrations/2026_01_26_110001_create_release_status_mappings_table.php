<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseStatusMappingsTable extends Migration
{
    public function up()
    {
        Schema::create('release_status_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('release_status_id');
            $table->string('cr_status_name'); // Maps to statuses.status_name
            $table->timestamps();
            
            $table->foreign('release_status_id')
                  ->references('id')
                  ->on('release_statuses')
                  ->onDelete('cascade');
                  
            $table->index('cr_status_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('release_status_mappings');
    }
}
