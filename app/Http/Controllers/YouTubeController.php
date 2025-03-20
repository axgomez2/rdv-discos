<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YouTubeController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $apiKey = config('services.youtube.api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'YouTube API key não configurada'], 500);
        }

        try {
            $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'q' => $query,
                'type' => 'video',
                'key' => $apiKey,
                'maxResults' => 5
            ]);

            if ($response->failed()) {
                throw new \Exception('Falha na requisição para a API do YouTube');
            }

            $results = $response->json()['items'];

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

