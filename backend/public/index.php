<?php

// Define the application base path
define('LARAVEL_START', microtime(true));

// This would normally use the autoloader from Composer, but we'll simplify for our example
// require __DIR__.'/../vendor/autoload.php';

// Basic validation for API routes
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Simple API response
header('Content-Type: application/json');

// Basic routing
if (strpos($requestUri, '/api/poems') === 0) {
    // Poems endpoint
    if ($requestMethod === 'GET') {
        echo json_encode([
            'status' => 'success',
            'data' => [
                [
                    'id' => 1,
                    'title' => 'قصيدة الصحراء',
                    'content' => 'هذه هي قصيدة عن الصحراء العربية الجميلة',
                    'type' => 'classical',
                    'created_at' => '2025-05-21'
                ],
                [
                    'id' => 2,
                    'title' => 'الشوق للوطن',
                    'content' => 'قصيدة تعبر عن الحنين والشوق للوطن الغالي',
                    'type' => 'nabati',
                    'created_at' => '2025-05-20'
                ]
            ]
        ]);
    } elseif ($requestMethod === 'POST') {
        // Get POST data
        $postData = json_decode(file_get_contents('php://input'), true);
        
        // Return success with the received data
        echo json_encode([
            'status' => 'success',
            'message' => 'تم إنشاء القصيدة بنجاح',
            'data' => [
                'id' => 3,
                'title' => $postData['title'] ?? 'قصيدة جديدة',
                'content' => $postData['content'] ?? 'محتوى القصيدة',
                'type' => $postData['type'] ?? 'classical',
                'created_at' => date('Y-m-d')
            ]
        ]);
    }
} elseif (strpos($requestUri, '/api/generate-poem') === 0) {
    // Generate poem endpoint
    if ($requestMethod === 'POST') {
        // Get POST data
        $postData = json_decode(file_get_contents('php://input'), true);
        
        // Mock poem generation
        echo json_encode([
            'status' => 'success',
            'data' => [
                'title' => 'قصيدة عن ' . ($postData['theme'] ?? 'الطبيعة'),
                'content' => "أبيات شعرية رائعة عن " . ($postData['theme'] ?? 'الطبيعة') . "\nهذه أبيات تم إنشاؤها بواسطة الذكاء الاصطناعي\nتعبر عن المشاعر والأحاسيس بكلمات راقية\nوتصور جمال الكون بعبارات بليغة",
                'type' => $postData['type'] ?? 'classical'
            ]
        ]);
    }
} else {
    // Default response for unknown routes
    echo json_encode([
        'status' => 'error',
        'message' => 'Route not found'
    ]);
    http_response_code(404);
}
