<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_viewers', function (Blueprint $table) {
            $table->id();
            $table->string('level', 191);
            $table->string('level_name', 191);
            $table->text('message');
            $table->string('user_agent', 3000)->nullable();
            $table->string('ip_address', 191)->nullable();
            $table->string('http_method', 10)->nullable();
            $table->text('url')->nullable();
            $table->text('referer_url')->nullable();
            $table->json('headers')->nullable();
            $table->json('context')->nullable();
            $table->json('extra')->nullable();
            $table->json('trace_stack')->nullable();
            $table->string('log_hash', 191)->nullable()->index();
            $table->unsignedTinyInteger('solved')->default(0);
            $table->foreignId('solved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('solved_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('level_name');
            $table->index('solved');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_viewers');
    }
};
