<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentImportColumnsToImportsTable extends Migration
{
    public function up()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->boolean('is_parent')->default(false);
            $table->foreignId('parent_import_id')->nullable()->constrained('imports')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropForeign(['parent_import_id']);
            $table->dropColumn(['is_parent', 'parent_import_id']);
        });
    }
} 