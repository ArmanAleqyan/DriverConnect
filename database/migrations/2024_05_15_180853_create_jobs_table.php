<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('yandex_id')->nullable();
            $table->string('short_id')->nullable();
            $table->string('status')->nullable();

            $table->string('provider')->nullable();
            $table->string('amenities')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('price')->nullable();

            $table->string('address_from')->nullable();
            $table->string('address_from_lat')->nullable();
            $table->string('address_from_long')->nullable();
            $table->string('mileage')->nullable();


            $table->string('price_in_minute')->nullable();
            $table->string('price_in_km')->nullable();
            $table->string('timeDifferenceFormatted')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('booked_at')->nullable();
            $table->timestamp('order_time_interval_from')->nullable();
            $table->timestamp('order_time_interval_to')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->unsignedBigInteger('car_category_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->foreign('car_category_id')->references('id')->on('car_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('car_id')->references('id')->on('user_cars')->onDelete('cascade');
//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
