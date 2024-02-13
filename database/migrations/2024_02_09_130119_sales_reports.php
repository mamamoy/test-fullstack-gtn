<?php

use App\Models\Customers;
use App\Models\SellingPackets;
use App\Models\User;
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
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();
            $table->string('sales_report_id')->unique();
            $table->unsignedBigInteger('appliedBy_id');
            $table->foreign('appliedBy_id')->references('id')->on('users')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Customers::class, 'customer_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SellingPackets::class, 'selling_packet_id')->constrained()->cascadeOnDelete();
            $table->boolean('isApproved')->default(0);
            $table->unsignedBigInteger('approvedBy_id')->nullable();
            $table->foreign('approvedBy_id')->references('id')->on('users')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
