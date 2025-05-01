<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('polygons', function (Blueprint $table) {
        $table->string('color')->default('#0080ff')->after('residents');
    });
}

public function down(): void
{
    Schema::table('polygons', function (Blueprint $table) {
        $table->dropColumn('color');
    });
}

};
