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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->text('address')->nullable();
            $table->string('normalized_business_name')->nullable();
            $table->string('normalized_area')->nullable();
            $table->string('normalized_city')->nullable();
            $table->string('normalized_address')->nullable();
            $table->string('duplicate_group')->nullable();
            $table->boolean('is_duplicate')->default(false);
            $table->string('merged_into')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('business_name');
            $table->index('city');
            $table->index('category');
            $table->index('area');
            $table->index('is_duplicate');
            $table->index('duplicate_group');
            $table->index(
                ['normalized_business_name', 'normalized_area', 'normalized_city', 'normalized_address'],
                'businesses_normalized_lookup_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
