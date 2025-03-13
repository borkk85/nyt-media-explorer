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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('nyt_id')->nullable();
            $table->string('title');
            $table->string('author');
            $table->text('description')->nullable();
            $table->string('isbn13', 13)->nullable();
            $table->string('isbn10', 10)->nullable();
            $table->string('publisher')->nullable();
            $table->string('image_url')->nullable();
            $table->date('published_date')->nullable();
            $table->string('amazon_url')->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->integer('weeks_on_list')->nullable();
            $table->string('list_name')->nullable();
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
