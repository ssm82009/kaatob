<?php

namespace App\Http\Controllers;

use App\Models\Poem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PoemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $poems = Poem::where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json($poems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'poem_text' => 'required|string',
            'poem_type' => 'required|string|in:classical,nabati',
            'keywords' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id() ?? 1; // Default to 1 for demo
        $validated['generated_with_model'] = 'manual';
        
        $poem = Poem::create($validated);
        
        return response()->json($poem, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $poem = Poem::findOrFail($id);
        
        // Increment view count
        $poem->increment('views');
        
        return response()->json($poem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $poem = Poem::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'string|max:255',
            'poem_text' => 'string',
            'poem_type' => 'string|in:classical,nabati',
            'keywords' => 'nullable|array',
            'is_public' => 'boolean',
        ]);
        
        $poem->update($validated);
        
        return response()->json($poem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $poem = Poem::findOrFail($id);
        $poem->delete();
        
        return response()->json(null, 204);
    }    /**
     * Generate a poem using AI.
     */
    public function generatePoem(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string|min:5',
            'poem_type' => 'required|string|in:classical,nabati',
            'keywords' => 'nullable|array',
            'max_length' => 'nullable|integer|min:100|max:2000',
        ]);

        // Get AI settings from database
        $apiKey = Setting::getByKey('gpt_api_key');
        $model = Setting::getByKey('gpt_model', 'gpt-4');
        $temperature = Setting::getByKey('gpt_temperature', 0.7);
        $maxTokens = Setting::getByKey('gpt_max_tokens', 1000);
        
        // If API key is not set, use mock generation
        if (empty($apiKey)) {
            Log::warning('No API key found, using mock generation');
            $response = $this->mockAIGeneration($validated);
            
            return response()->json([
                'success' => true,
                'poem' => $response['poem'],
                'title' => $response['title'],
                'model' => 'mock'
            ]);
        }
          try {
            // Use the OpenAI API for real poem generation
            $response = $this->generateWithOpenAI($validated, $model, $temperature, $maxTokens, $apiKey);
            
            return response()->json([
                'success' => true,
                'poem' => $response['poem'],
                'title' => $response['title'],
                'model' => $model
            ]);
        } catch (\Exception $e) {
            Log::error('Poem generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate poem.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mock AI poem generation (for demo without actual API call)
     */
    private function mockAIGeneration(array $data)
    {
        $poemType = $data['poem_type'];
        $prompt = $data['prompt'];
        $keywords = $data['keywords'] ?? [];
        
        $keywordsText = count($keywords) > 0 ? ' عن ' . implode('، ', $keywords) : '';
        
        $poems = [
            'classical' => [
                'title' => 'قصيدة في ' . $prompt,
                'poem' => "وقفتُ على الأطلالِ أبكي لِمَن مضى\nوأندبُ عهداً بالحمى غيرَ راجعِ\nوكم ذرفت عيني على الدار دمعةً\nفلم يُبقِ دمعي في جفوني مدامعي\n\nتذكرتُ ليلاتٍ بذي سَلَمٍ لنا\nوأيامَ كانت بالعقيق مراتعي\nفلله أيامٌ مضت ولياليها\nمضت وكأن الوصل أضغاثُ نائمِ"
            ],
            'nabati' => [
                'title' => 'قصيدة نبطية في ' . $prompt,
                'poem' => "يا هاجسي لا تلوم القلب واعذره\nما كل جرحٍ على مر الزمن يبرى\nوش تنفع الكلمات اللي تزخرفها\nدام الحكي ما يداوي جرح من يهوى\n\nقلبي عليك انشغل وانته ناسيني\nوكل ما طاح نجمٍ زاد بي شوقي\nالله يسامح زمانٍ فرق الخلان\nوالله يسامح غيابك ليه تقسى"
            ]
        ];
        
        // In a real implementation, you would call an actual AI API here
        
        // Simulate API processing time
        sleep(1);
        
        return $poems[$poemType];
    }

    /**
     * Generate poem with OpenAI API
     * 
     * @param array $data The user input data
     * @param string $model The GPT model to use
     * @param float $temperature The temperature setting
     * @param int $maxTokens Maximum tokens to generate
     * @param string $apiKey OpenAI API key
     * @return array The generated poem response
     * @throws \Exception If API call fails
     */
    private function generateWithOpenAI(array $data, string $model, float $temperature, int $maxTokens, string $apiKey)
    {
        $poemType = $data['poem_type'];
        $prompt = $data['prompt'];
        $keywords = $data['keywords'] ?? [];
        $maxLength = $data['max_length'] ?? 1000;
        
        // Build system prompt based on poem type
        $systemPrompt = $poemType === 'classical' 
            ? 'أنت شاعر عربي مختص في الشعر العربي الفصيح. مهمتك إنشاء قصائد فصيحة عربية تتميز بالبلاغة والفصاحة وجمال الأسلوب.'
            : 'أنت شاعر عربي مختص في الشعر النبطي. مهمتك إنشاء قصائد نبطية أصيلة تتميز بالبلاغة وجمال الأسلوب.';
        
        // Format keywords if any
        $keywordsText = count($keywords) > 0 ? ' مع تضمين الكلمات التالية: ' . implode('، ', $keywords) : '';
        
        // Create user prompt with instructions
        $userPrompt = "اكتب قصيدة " . ($poemType == 'classical' ? 'فصيحة' : 'نبطية') . " عن موضوع: {$prompt}{$keywordsText}. ";
        $userPrompt .= "اجعل القصيدة لا تزيد عن {$maxLength} حرف، وأعطها عنواناً مناسباً.";
        
        // Additional instructions for formatting
        $userPrompt .= " قم بفصل الأبيات بسطر جديد واستخدم اللغة العربية الفصحى.";
        
        try {
            // Setup API request
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ],
                'timeout' => 30, // Increase timeout for longer poems
            ]);
            
            // Process response
            $result = json_decode($response->getBody(), true);
            
            if (!isset($result['choices'][0]['message']['content'])) {
                \Log::error('Invalid OpenAI response structure', ['response' => $result]);
                throw new \Exception('استجابة غير صالحة من واجهة برمجة التطبيقات الذكاء الاصطناعي.');
            }
            
            $generatedText = $result['choices'][0]['message']['content'];
            
            // Extract title and poem text
            $lines = explode("\n", $generatedText);
            $title = trim($lines[0]);
            
            // Remove title from poem text if it's in the first line
            if (count($lines) > 1) {
                array_shift($lines);
                $poemText = trim(implode("\n", $lines));
            } else {
                $title = 'قصيدة في ' . $prompt;
                $poemText = $generatedText;
            }
            
            return [
                'title' => $title,
                'poem' => $poemText
            ];
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            \Log::error('OpenAI API request failed', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : null
            ]);
            
            // Fall back to mock generation if API fails
            if (config('app.env') !== 'production') {
                \Log::info('Falling back to mock generation due to API failure');
                return $this->mockAIGeneration($data);
            }
            
            throw new \Exception('فشل الاتصال بواجهة برمجة التطبيقات الذكاء الاصطناعي: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('OpenAI generation error', ['error' => $e->getMessage()]);
            throw new \Exception('حدث خطأ أثناء إنشاء القصيدة: ' . $e->getMessage());
        }
    }
}
