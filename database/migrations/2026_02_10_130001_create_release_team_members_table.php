<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseTeamMembersTable extends Migration
{
    public function up()
    {
        Schema::create('release_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('release_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->string('mobile', 11);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('release_team_roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('release_team_members');
    }
}
