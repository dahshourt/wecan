<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RevampReleasesTableV2 extends Migration
{
    public function up()
    {
        Schema::table('releases', function (Blueprint $table) {
            // Foreign Keys
            $table->unsignedBigInteger('vendor_id')->nullable()->after('name');
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('target_system_id')->nullable();
            $table->unsignedBigInteger('responsible_rtm_id')->nullable();
            
            // Creator Info
            $table->string('creator_rtm_name')->nullable();
            $table->string('rtm_email')->nullable();
            $table->text('release_description')->nullable();
            
            // All Date Fields
            $table->date('release_start_date')->nullable();
            $table->date('atp_review_start_date')->nullable();
            $table->date('atp_review_end_date')->nullable();
            $table->date('vendor_internal_test_start_date')->nullable();
            $table->date('vendor_internal_test_end_date')->nullable();
            $table->date('iot_start_date')->nullable();
            $table->date('iot_end_date')->nullable();
            $table->date('e2e_start_date')->nullable();
            $table->date('e2e_end_date')->nullable();
            $table->date('uat_start_date')->nullable();
            $table->date('uat_end_date')->nullable();
            $table->date('smoke_test_start_date')->nullable();
            $table->date('smoke_test_end_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('releases', function (Blueprint $table) {
            $table->dropColumn([
                'vendor_id', 'priority_id', 'target_system_id', 'responsible_rtm_id',
                'creator_rtm_name', 'rtm_email', 'release_description',
                'release_start_date', 'atp_review_start_date', 'atp_review_end_date',
                'vendor_internal_test_start_date', 'vendor_internal_test_end_date',
                'iot_start_date', 'iot_end_date', 'e2e_start_date', 'e2e_end_date',
                'uat_start_date', 'uat_end_date', 'smoke_test_start_date', 'smoke_test_end_date'
            ]);
        });
    }
}
