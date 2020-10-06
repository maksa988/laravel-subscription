<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subscription_plan_id');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->integer('price')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('prolong_next_period')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            /*
             * Foreign keys
             */
            $table->foreign('subscription_plan_id')
                ->references('id')->on('subscription_plans')
                ->onDelete('cascade');

            $table->foreign('coupon_id')
                ->references('id')->on('coupons')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
}
