<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->text('logs')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->json('logs')->nullable()->change();
        });
    }
};