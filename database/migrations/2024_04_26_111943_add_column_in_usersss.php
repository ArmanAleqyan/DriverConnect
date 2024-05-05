<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInUsersss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_license_country_id')->nullable();
            $table->foreign('driver_license_country_id')->references('id')->on('driver_licenses')->onDelete('SET NULL');
//            $table->string('driver_license_front_photo')->nullable();
//            $table->string('driver_license_back_photo')->nullable();
//            $table->string('scanning_name')->nullable();
//            $table->string('scanning_surname')->nullable();
//            $table->string('scanning_middle_name')->nullable();
//            $table->string('scanning_birth_date')->nullable();
//            $table->string('driver_license_expiry_date')->nullable();
//            $table->string('driver_license_issue_date')->nullable();
//            $table->string('driver_license_number')->nullable();
//            $table->string('driver_license_experience_total_since_date')->nullable();
//            $table->string('sended_in_yandex_status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usersss', function (Blueprint $table) {
            //
        });
    }
}
