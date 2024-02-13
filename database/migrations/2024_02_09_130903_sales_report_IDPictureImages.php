<?php

use App\Models\SalesReports;
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
        Schema::create('sales_report_id_picture_images', function (Blueprint $table) {
            $table->id();
            $table->string('identification_id_image');
            $table->foreignIdFor(SalesReports::class, 'sales_report_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_report_id_picture_images');
    }
};
