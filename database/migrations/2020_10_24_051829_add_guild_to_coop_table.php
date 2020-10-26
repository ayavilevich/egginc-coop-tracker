<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuildToCoopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coops', function (Blueprint $table) {
            $table->bigInteger('guild_id')->nullable()->after('coop');
        });

        DB::table('coops')->update(['guild_id' => 722987744774848556]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coops', function (Blueprint $table) {
            $table->dropColumn('guild_id');
        });
    }
}
