<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseFeedbacksTable extends Migration
{
    public function up()
    {
        Schema::create('release_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('release_id');
            $table->text('feedback');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('release_feedbacks');
    }
}
