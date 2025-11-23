<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name');
            $table->unsignedBigInteger('donation_type_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable(); // for cash donations
            $table->string('items')->nullable(); // for goods description
            $table->date('donation_date')->default(now());
            $table->enum('status', ['received','pending','distributed'])->default('received');
            $table->timestamps();

            $table->foreign('donation_type_id')
                  ->references('id')->on('donation_types')
                  ->onDelete('set null');
        });
    }
    public function down(): void {
        Schema::dropIfExists('donations');
    }
};