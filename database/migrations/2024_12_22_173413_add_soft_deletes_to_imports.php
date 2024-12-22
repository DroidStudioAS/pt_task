<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToImports extends Migration
{
    public function up()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
} 