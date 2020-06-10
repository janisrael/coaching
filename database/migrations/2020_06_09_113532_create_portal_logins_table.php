<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortalLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portal_logins', function (Blueprint $table) {
            $table->id();
            $table->integer('portal_user_id')->unsigned();
            $table->text('portal_user_details')->nullable();
            $table->string('api_token', 191)->unique();
            $table->timestamp('expired_at', 0)->nullable();
            $table->timestamp('last_login_at', 0)->nullable();
            $table->ipAddress('last_login_ip', 16)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portal_logins');
    }
}
