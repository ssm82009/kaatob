<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get API settings
     */
    public function getAiSettings()
    {
        $settings = [
            'api_key' => Setting::getByKey('gpt_api_key', ''),
            'model' => Setting::getByKey('gpt_model', 'gpt-4'),
            'temperature' => Setting::getByKey('gpt_temperature', 0.7),
            'max_tokens' => Setting::getByKey('gpt_max_tokens', 1000)
        ];
        
        // Don't send the full API key to frontend
        if ($settings['api_key']) {
            $settings['api_key'] = '********' . substr($settings['api_key'], -4);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }
    
    /**
     * Update AI settings
     */
    public function updateAiSettings(Request $request)
    {
        // Validate request
        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:1',
            'max_tokens' => 'required|integer|min:50|max:4000'
        ]);
        
        // Update settings
        if ($request->api_key) {
            Setting::setByKey('gpt_api_key', $request->api_key, 'GPT API Key');
        }
        
        Setting::setByKey('gpt_model', $request->model, 'GPT Model');
        Setting::setByKey('gpt_temperature', $request->temperature, 'GPT Temperature');
        Setting::setByKey('gpt_max_tokens', $request->max_tokens, 'GPT Max Tokens');
        
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث إعدادات الذكاء الاصطناعي بنجاح'
        ]);
    }
}
