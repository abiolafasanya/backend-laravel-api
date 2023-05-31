<?php 

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class SearchApiController extends Controller {

    public function search(Request $request)
    {
        $keywords = $request->query('keywords', '');
        
        $results = $this->searchNews($keywords);
        
        return response()->json($results);
    }
    
    
    public function searchNews($keywords)
    {
        $cacheKey = 'search_' . md5($keywords);
        $cachedData = Cache::get($cacheKey);
        
        if ($cachedData !== null) {
            return $cachedData;
        }
        
        $url = 'https://newsapi.org/v2/everything';
        $token = config('articles.newsapi.key');
        $response = Http::get($url, [
            'apiKey' => $token,
            'q' => $keywords,
            'pageSize' => 6,
            ]);
            
        if ($response->successful()) {
            $articles = $response->json()['articles'] ?? [];
            $data = collect($articles)->map(function ($article) {
                return [
                    'source' => $article['source']['name'],
                    'publishedAt' => $article['publishedAt'],
                    'title' => $article['title'],
                    'description' => $article['description'],
                    'url' => $article['url'],
                    'image' => $article['urlToImage'] ?? null,
                ];
            })->toArray();

            // Cache the data for 30 minutes
            Cache::put($cacheKey, $data, 30 * 60);

            return $data;
        }

        return [];
    }
}

