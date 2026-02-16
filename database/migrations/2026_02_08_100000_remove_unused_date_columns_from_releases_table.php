<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedDateColumnsFromReleasesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('releases', function (Blueprint $table) {
            // Drop unused IOT date columns
            $table->dropColumn('planned_start_iot_date');
            $table->dropColumn('planned_end_iot_date');
            $table->dropColumn('actual_start_iot_date');
            $table->dropColumn('actual_end_iot_date');
            
            // Drop unused E2E date columns
            $table->dropColumn('planned_start_e2e_date');
            $table->dropColumn('planned_end_e2e_date');
            $table->dropColumn('actual_start_e2e_date');
            $table->dropColumn('actual_end_e2e_date');
            
            // Drop unused UAT date columns
            $table->dropColumn('planned_start_uat_date');
            $table->dropColumn('planned_end_uat_date');
            $table->dropColumn('actual_start_uat_date');
            $table->dropColumn('actual_end_uat_date');
            
            // Drop unused Smoke Test date columns
            $table->dropColumn('planned_start_smoke_test_date');
            $table->dropColumn('planned_end_smoke_test_date');
            $table->dropColumn('actual_start_smoke_test_date');
            $table->dropColumn('actual_end_smoke_test_date');
            
            // Drop actual closure date
            $table->dropColumn('actual_closure_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('releases', function (Blueprint $table) {
            // Re-add IOT date columns
            $table->date('planned_start_iot_date')->nullable();
            $table->date('planned_end_iot_date')->nullable();
            $table->date('actual_start_iot_date')->nullable();
            $table->date('actual_end_iot_date')->nullable();
            
            // Re-add E2E date columns
            $table->date('planned_start_e2e_date')->nullable();
            $table->date('planned_end_e2e_date')->nullable();
            $table->date('actual_start_e2e_date')->nullable();
            $table->date('actual_end_e2e_date')->nullable();
            
            // Re-add UAT date columns
            $table->date('planned_start_uat_date')->nullable();
            $table->date('planned_end_uat_date')->nullable();
            $table->date('actual_start_uat_date')->nullable();
            $table->date('actual_end_uat_date')->nullable();
            
            // Re-add Smoke Test date columns
            $table->date('planned_start_smoke_test_date')->nullable();
            $table->date('planned_end_smoke_test_date')->nullable();
            $table->date('actual_start_smoke_test_date')->nullable();
            $table->date('actual_end_smoke_test_date')->nullable();
            
            // Re-add actual closure date
            $table->date('actual_closure_date')->nullable();
        });
    }
}
