<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PoliticsController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'politics_news';
        $cacheDuration = 30; // in minutes

        $articles = Cache::remember($cacheKey, $cacheDuration, function () use ($request) {
            $url = 'https://newsapi.org/v2/top-headlines';
            $token = config('articles.newsapi.key');
            $country = $request->query('country', 'us');

            $response = Http::get($url, [
                'apiKey' => $token,
                'category' => 'politics',
                'country' => $country,
            ]);

            if ($response->successful()) {
                return collect($response->json()['articles'] ?? [])->map(function ($article) {
                    return [
                        'source' => $article['source']['name'],
                        'publishedAt' => $article['publishedAt'],
                        'title' => $article['title'],
                        'description' => $article['description'],
                        'url' => $article['url'],
                        'image' => $article['urlToImage'] ?? null,
                    ];
                })->toArray();
            }

            return [];
        });

        return $articles;
    }
}
