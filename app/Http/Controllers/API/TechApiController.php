<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TechApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheKey = 'tech_news';
        $cacheDuration = 30; // in minutes

        $articles = Cache::remember($cacheKey, $cacheDuration, function () {
            $url = 'https://newsapi.org/v2/top-headlines';
            $token = config('articles.newsapi.key');
            $sources = ['techcrunch', 'engadget', 'the-verge', 'Google News'];
            $response = Http::get($url, [
                'apiKey' => $token,
                'sources' => implode(',', $sources),
                'perPage' => 40,
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
