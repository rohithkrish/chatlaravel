<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function streamChatGPT($chatHistory, $callback)
    {
        // die(var_dump($chatHistory));
        $url = "https://api.openai.com/v1/chat/completions";
        $payload1 = [
            'model' => 'gpt-4o-mini',
            'messages' => $chatHistory,
            'temperature' => 0.7,
            'max_tokens' => 1000,
            'stream' => true,
        ];
    
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload1,
                'stream' => true,
            ]);
    
            $buffer = '';
            $body = $response->getBody();
    
            // ✅ Start output buffering
            ob_start();
    
            while (!$body->eof()) {
                $chunk = $body->read(1024);
                $buffer .= $chunk;
    
                $lines = explode("\n", $buffer);
                $buffer = array_pop($lines); // Keep last incomplete part in buffer
    
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (str_starts_with($line, "data: ")) {
                        $json = substr($line, 6);
    
                        if (!empty($json)) {
                            $decoded = json_decode($json, true);
    
                            if (isset($decoded['choices'][0]['delta']['content'])) {
                                $text = $decoded['choices'][0]['delta']['content'];
                                if (!empty($text)) {
                                    $callback($text);
    
                                    // ✅ Ensure buffering and immediate output
                                    echo str_pad('', 4096) . "\n"; // Send empty padding for chunked transfer
                                    ob_flush();
                                    flush();
                                }
                            }
                        }
                    }
                }
            }
            ob_end_flush(); // ✅ End buffering after the loop
        } catch (GuzzleException $e) {
            $callback(json_encode(['error' => $e->getMessage()]));
        }
    }
    
}
