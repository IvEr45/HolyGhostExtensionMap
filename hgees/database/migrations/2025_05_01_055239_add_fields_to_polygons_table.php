<?php
// database/migrations/YYYY_MM_DD_add_fields_to_polygons_table.php
// Create this new migration file using: php artisan make:migration add_fields_to_polygons_table

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
            $table->string('house_number')->nullable()->after('coordinates');
            $table->string('residents')->nullable()->after('house_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polygons', function (Blueprint $table) {
            $table->dropColumn('house_number');
            $table->dropColumn('residents');
        });
    }
};