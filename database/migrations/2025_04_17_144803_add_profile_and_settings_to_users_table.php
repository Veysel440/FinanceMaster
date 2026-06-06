<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Idempotent: the consolidated users migration (0001_01_01_000000)
        // already adds these columns on fresh installs. Older deployments
        // that ran the original create_users without them still need this.
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'currency')) {
                $table->string('currency', 3)->default('TRY')->after('profile_photo');
            }
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)->default('tr')->after('currency');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(
                ['profile_photo', 'currency', 'locale'],
                fn ($col) => Schema::hasColumn('users', $col)
            );
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
