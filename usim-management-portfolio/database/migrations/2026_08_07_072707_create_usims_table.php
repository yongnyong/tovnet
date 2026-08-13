<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usims', function (Blueprint $table) {
            $table->id();
            $table->string('usim_number')->unique()->comment('유심 일련번호(ICCID)');
            $table->string('phone_number')->nullable();
            $table->string('carrier')->nullable()->comment('통신사');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('device_id')->nullable()->unique()->constrained('devices')->nullOnDelete();
            $table->enum('status', ['계약', '일시정지', '해지'])->default('계약');
            $table->date('contract_date')->nullable();
            $table->date('suspended_date')->nullable();
            $table->date('canceled_date')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->index('usim_number');
            $table->index('phone_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usims');
    }
};
