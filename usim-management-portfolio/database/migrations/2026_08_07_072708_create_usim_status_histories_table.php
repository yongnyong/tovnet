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
        Schema::create('usim_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usim_id')->constrained('usims')->cascadeOnDelete();
            $table->enum('status', ['계약', '일시정지', '해지']);
            $table->date('changed_date');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->index('usim_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usim_status_histories');
    }
};
