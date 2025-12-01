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
    Schema::create('form_submissions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
      $table->json('submission_data');
      $table->boolean('is_quarantined')->default(false);
      $table->float('recaptcha_score')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('form_submissions');
  }
};
