<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use App\Models\ChatHistory;

class ChatController extends Controller
{
    protected $chatGptService;

    public function __construct(ChatService $chatGptService)
    {
        $this->chatGptService = $chatGptService;
    }

    public function streamChat(Request $request)
    {
        

        // Log::info('Session before update: ', session()->all());
        // Log::info('Appending user prompt: ', ['prompt' => $request->input('prompt')]);
        $chatHistory = session()->get('chat_history',[]);
        $arrSaveChat = [];
        $id = $request->input('id'   ); 
        $promptMessage = $request->input('prompt');
        if(trim($promptMessage) == '' && !is_null($id)){
           $chatDetails =   ChatHistory::find($id); 
            $chatDetails = $chatDetails->conversation;
            $useMessage =$chatDetails[0]['content'];
            $apiMessage =$chatDetails[1]['content'];
            $chatHistory[] = ['role' => 'assistant', 'content' => trim($apiMessage)];
            $chatHistory[] = ['role' => 'user', 'content' => $useMessage];
            $arrSaveChat[] = ['role' => 'user', 'content' => $useMessage];     
        }
        else
        {
            $chatHistory[] = ['role' => 'user', 'content' => $promptMessage];
            $arrSaveChat[] = ['role' => 'user', 'content' => $promptMessage];
        }
       

        return response()->stream(function ()  use ($chatHistory, $arrSaveChat)  {
            // Ensure no output buffering is interfering
            while (@ob_end_flush());
            $fullResponse = "";
            $this->chatGptService->streamChatGPT($chatHistory, function ($chunk) use (&$fullResponse) {
                // Proper SSE format
               
                echo  trim($chunk) . "\n\n"; // Each chunk as SSE message

                $fullResponse .= $chunk;
               
            });

          
            if (!empty(trim($fullResponse))) {
                Log::info('prob: '.$fullResponse);
                $chatHistory[] = ['role' => 'assistant', 'content' => trim($fullResponse)];
                $arrSaveChat[] = ['role' => 'assistant', 'content' => trim($fullResponse)];
               
                ChatHistory::create(['conversation' => $arrSaveChat]);

                session()->put('chat_history', $chatHistory);
                session()->save();


            }
            // Send a closing event when the stream ends
          
            echo "data: [DONE]\n\n";
          
        }, 200, [
            'Content-Type'  => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection'    => 'keep-alive',
        ]);
    }


    public function getHistory()
    {
        $latestChats = ChatHistory::orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json($latestChats);
    }
}
