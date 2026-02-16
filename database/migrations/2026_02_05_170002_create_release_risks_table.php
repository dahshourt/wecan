<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseRisksTable extends Migration
{
    public function up()
    {
        Schema::create('release_risks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('risk_number'); // For RSK-001 format
            $table->unsignedBigInteger('release_id');
            $table->unsignedBigInteger('cr_id')->nullable();
            $table->text('risk_description');
            $table->unsignedBigInteger('risk_category_id');
            $table->unsignedTinyInteger('impact_level'); // 1-5
            $table->unsignedTinyInteger('probability'); // 1-5
            $table->unsignedTinyInteger('risk_score'); // impact * probability
            $table->string('owner')->nullable(); // mail
            $table->unsignedBigInteger('risk_status_id');
            $table->text('mitigation_plan')->nullable();
            $table->text('contingency_plan')->nullable();
            $table->date('date_identified')->nullable();
            $table->date('target_resolution_date')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->foreign('cr_id')->references('id')->on('change_request')->onDelete('set null');
            $table->foreign('risk_category_id')->references('id')->on('risk_categories');
            $table->foreign('risk_status_id')->references('id')->on('risk_statuses');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Index for quick lookups
            $table->index(['release_id', 'risk_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('release_risks');
    }
}
