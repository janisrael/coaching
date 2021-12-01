<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanAccessCoachingColumnToPortalLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_logins', function (Blueprint $table) {
            $table->boolean('can_access_coaching')->default(false)->nullable()->after('api_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_logins', function (Blueprint $table) {
            $table->dropColumn('can_access_coaching');
        });
    }
}
