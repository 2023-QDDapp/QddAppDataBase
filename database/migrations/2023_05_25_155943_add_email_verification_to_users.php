<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailVerificationToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_token')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_registered')->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_token');
            $table->dropColumn('is_verified');
            $table->dropColumn('is_registered');
        });
    }
}
