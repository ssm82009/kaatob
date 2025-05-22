<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poems', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('poem_text');
            $table->string('poem_type')->default('classical'); // classical or nabati
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('keywords')->nullable();
            $table->string('generated_with_model')->nullable();
            $table->boolean('is_public')->default(true);
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poems');
    }
}
