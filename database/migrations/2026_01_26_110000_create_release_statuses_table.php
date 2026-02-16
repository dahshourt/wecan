<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseStatusesTable extends Migration
{
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('release_statuses');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::create('release_statuses', function (Blueprint $table) {
            
            $table->id();
            $table->string('name');
            $table->integer('display_order')->default(0);
            $table->string('color', 7)->default('#6c757d'); // Hex color
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('release_statuses');
    }
}
