<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('imported_data', function (Blueprint $table) {
            $table->foreignId('import_id')->nullable()->after('id')
                  ->constrained('imports')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('imported_data', function (Blueprint $table) {
            $table->dropForeign(['import_id']);
            $table->dropColumn('import_id');
        });
    }
}; 