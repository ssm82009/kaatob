<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $this->seedDefaultSettings();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Seed default settings.
     *
     * @return void
     */
    private function seedDefaultSettings()
    {
        DB::table('settings')->insert([
            [
                'key' => 'gpt_api_key',
                'value' => null,
                'description' => 'OpenAI API Key for GPT integration',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'gpt_model',
                'value' => 'gpt-4',
                'description' => 'GPT Model to use for poem generation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'gpt_temperature',
                'value' => '0.7',
                'description' => 'Temperature setting for GPT (controls randomness)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'gpt_max_tokens',
                'value' => '1000',
                'description' => 'Maximum tokens per request',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
