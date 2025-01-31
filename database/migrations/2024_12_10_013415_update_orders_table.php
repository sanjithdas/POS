<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns
            $table->dateTime('payment_date')->nullable(); // Payment date, nullable initially
            $table->string('payment_type')->nullable(); // Payment type (e.g., "stripe", "paypal"), nullable initially

            // Modify the 'status' column enum
          //  $table->enum('status', ['pending', 'processing', 'paid', 'cancelled'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn('payment_date');
            $table->dropColumn('payment_type');

            // Revert the 'status' column enum back to original
        //    $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending')->change();
        });
    }
};
