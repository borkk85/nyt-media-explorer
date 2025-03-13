<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('display_title');
            $table->string('byline')->nullable(); // reviewer
            $table->string('headline')->nullable();
            $table->text('summary')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('mpaa_rating')->nullable();
            $table->boolean('critics_pick')->default(false);
            $table->string('image_url')->nullable();
            $table->string('nyt_url')->nullable();
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
