<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReleasesReleaseStatusColumn extends Migration
{
    public function up()
    {
        Schema::table('releases', function (Blueprint $table) {
            // Note: The existing release_status column may be integer pointing to old table
            // We'll add a new column and can migrate data later if needed
            if (!Schema::hasColumn('releases', 'release_status_id')) {
                $table->unsignedBigInteger('release_status_id')->nullable()->after('release_status');
            }
        });
    }

    public function down()
    {
        Schema::table('releases', function (Blueprint $table) {
            if (Schema::hasColumn('releases', 'release_status_id')) {
                $table->dropColumn('release_status_id');
            }
        });
    }
}
