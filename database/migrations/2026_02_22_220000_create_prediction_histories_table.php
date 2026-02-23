<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediction_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('predicted_class');
            $table->decimal('confidence', 8, 6);
            $table->json('raw_scores');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_histories');
    }
};
