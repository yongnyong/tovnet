<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Widen enum first so both old and new values are valid while data is migrated.
        DB::statement("ALTER TABLE usims MODIFY status ENUM('계약','사용중','일시정지','해지') NOT NULL DEFAULT '사용중'");
        DB::statement("ALTER TABLE usim_status_histories MODIFY status ENUM('계약','사용중','일시정지','해지') NOT NULL");

        DB::table('usims')->where('status', '계약')->update(['status' => '사용중']);
        DB::table('usim_status_histories')->where('status', '계약')->update(['status' => '사용중']);

        // Narrow enum to the final set.
        DB::statement("ALTER TABLE usims MODIFY status ENUM('사용중','일시정지','해지') NOT NULL DEFAULT '사용중'");
        DB::statement("ALTER TABLE usim_status_histories MODIFY status ENUM('사용중','일시정지','해지') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE usims MODIFY status ENUM('계약','사용중','일시정지','해지') NOT NULL DEFAULT '계약'");
        DB::statement("ALTER TABLE usim_status_histories MODIFY status ENUM('계약','사용중','일시정지','해지') NOT NULL");

        DB::table('usims')->where('status', '사용중')->update(['status' => '계약']);
        DB::table('usim_status_histories')->where('status', '사용중')->update(['status' => '계약']);

        DB::statement("ALTER TABLE usims MODIFY status ENUM('계약','일시정지','해지') NOT NULL DEFAULT '계약'");
        DB::statement("ALTER TABLE usim_status_histories MODIFY status ENUM('계약','일시정지','해지') NOT NULL");
    }
};
