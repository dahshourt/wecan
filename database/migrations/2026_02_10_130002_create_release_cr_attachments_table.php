<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseCrAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('release_cr_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('release_id');
            $table->unsignedBigInteger('cr_id');
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->foreign('cr_id')->references('id')->on('change_request')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('release_cr_attachments');
    }
}
