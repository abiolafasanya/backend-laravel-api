<?php

namespace App\Http\Controllers\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Traits\FetchesArticlesTrait;
use App\Traits\FiltersArticlesTrait;
use Illuminate\Support\Facades\Http;
use App\Traits\PaginateArticlesTrait;
use App\Traits\SearchesArticlesTrait;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\NewsApiResource;

class NewsService
{
    use FetchesArticlesTrait, FiltersArticlesTrait, SearchesArticlesTrait, PaginateArticlesTrait;


    public function general(Request $request)
    {
      
            $urls = [
                'https://newsapi.org/v2/everything?domains=wsj.com',
                'https://newsapi.org/v2/top-headlines?country=ng',
                'https://newsapi.org/v2/top-headlines?country=us',
                'https://newsapi.org/v2/top-headlines?category=sports&language=en',
                'https://newsapi.org/v2/top-headlines?sources=techcrunch',
            ];

            $token = config('articles.newsapi.key');
            $articles = $this->fetchArticles($urls, $token);

            $filteredArticles = $this->applyFilters($articles, $request);

            $perPage = intval($request->query('perPage', 10));

            $paginator = $this->paginate(collect($filteredArticles), $perPage);

            $response = [];

            foreach ($paginator as $item) {
                $id = Uuid::uuid4()->toString();
                $response[] = [
                    'id' => $item['id'] ?? $id,
                    'source' => $item['source']['name'],
                    'publishedAt' => $item['publishedAt'],
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'image' => $item['urlToImage'] ?? null,
                ];
            }

            return $response;

    }
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keywords' => 'required|string',
            'page' => 'nullable|integer|min:1',
        ]);

        $cacheKey = 'search_' . $validatedData['keywords'];
        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult) {
            return $cachedResult;
        }

        $urls = [
            'https://newsapi.org/v2/top-headlines?country=ng',
            'https://newsapi.org/v2/top-headlines?country=us',
            'https://newsapi.org/v2/top-headlines?category=sports&language=en'
        ];

        $token = config('articles.newsapi.key');
        $articles = $this->fetchArticles($urls, $token);

        $filteredArticles = $this->applyFilters($articles, $request);

        $searchedArticles = $this->searchArticles($filteredArticles, $validatedData['keywords']);

        $perPage = 10;
        $paginator = $this->paginate(collect($searchedArticles), $perPage);
        $newsResource = NewsApiResource::collection($paginator);

        $cacheDuration = now()->addMinutes(5); // Set the caching duration (e.g., 5 minutes)
        Cache::put($cacheKey, $newsResource->response()->setStatusCode(200), $cacheDuration);

        return $newsResource->response()->setStatusCode(200);
    }

    public function headlines(Request $request)
    {
        $cacheKey = 'headlines';
        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult) {
            return $cachedResult;
        }

        $query_country = $request->query('q');
        $query = '?country=us&category=business&publisher=bbc&source=bbc';
        $url = 'https://newsapi.org/v2/top-headlines' . $query;
        $token = config('articles.newsapi.key');
        $articles = $this->fetchArticles($url, $token);
        $filteredArticles = $this->applyFilters($articles, $request);

        $perPage = 20;
        $paginator = $this->paginate(collect($filteredArticles), $perPage);
        $response = [];
        foreach ($paginator as $item) {
            $id = Uuid::uuid4()->toString();
            $response[] = [
                'id' => $id,
                'source' => $item['source']['name'],
                'publishedAt' => $item['publishedAt'],
                'title' => $item['title'],
                'description' => $item['description'],
                'image' => $item['urlToImage'] ?? null,
            ];
        }

        $cacheDuration = now()->addMinutes(30); // Set the caching duration (e.g., 10 minutes)
        Cache::put($cacheKey, $response, $cacheDuration);
        return $response;
    }


    public function nytimes()
    {
        $url = 'https://api.nytimes.com/svc/mostpopular/v2/emailed/7.json';

        $result = Http::get($url, [
            'api-key' => config('articles.nytimes.key'),
        ]);
        $collect = collect($result->json());

        $response = [];

        foreach ($collect['results'] as $item) {
            $response[] = [
                'id' => $item['id'],
                'source' => $item['source'],
                'publishedAt' => $item['published_date'],
                'title' => $item['title'],
                'description' => $item['abstract'],
                'image' => $item['media'][0]['media-metadata'][0]['url'] ?? null,
            ];
        }
        return $response;
    }

    /**
     * Search New York Times Articles.
     */
    public function nytimes_search(Request $request)
    {
        $searchQuery = $request->input('query');
        $url = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';

        $result = Http::get($url, [
            'q' => $searchQuery,
            'api-key' => config('articles.nytimes.key'),
        ]);
        $collect = collect($result->json());

        $response = [];

        foreach ($collect['response']['docs'] as $item) {
            $response[] = [
                'id' => $item['_id'],
                'source' => $item['source'],
                'publishedAt' => $item['pub_date'],
                'title' => $item['headline']['main'],
                'description' => $item['abstract'],
                'image' => null, // Update this based on available data in the API response.
            ];
        }

        return response()->json($response);
    }


    public function guadians(Request $request)
    {
        $url = 'https://content.guardianapis.com/search';
        $response = Http::get($url, [
            'api-key' => config('articles.guardians.key'),
            'q' => $request->query('q', 'headline'),
        ]);

        $articles = collect($response->json()['response']['results'])->map(function ($article) {
            $unsplashUrl = 'https://api.unsplash.com/search/photos';

            $imageResponse = Http::withHeaders([
                'Authorization' => 'Client-ID ' . config('articles.unsplash.key'),
            ])->get($unsplashUrl, [
                'query' => $article['webTitle'],
            ]);

            $imageData = $imageResponse->json()['results'][0] ?? null;
            return [
                'id' => $article['id'],
                'title' => $article['webTitle'],
                'image' => $imageData ? $imageData['urls']['regular'] : null, // $article['fields']['thumbnail'] ?? null,
                'description' => $article['fields']['trailText'] ?? null,
                'publishedAt' => $article['webPublicationDate'],
                'rating' => $article['fields']['starRating'] ?? null,
                'source' => $article['webUrl'],
            ];
        });
        return $articles;
    }


    public function fetchSources()
    {
        $url = 'https://newsapi.org/v2/top-headlines/sources';
        $token = config('articles.newsapi.key');

        $articles = Http::get($url, [
            'apiKey' => $token,
        ]);
          //  $articles = $this->fetchArticles($url, $token);

        if ($articles->successful()) {
            $sources = $articles->json()['sources'] ?? [];
            return collect($sources)->map(function ($source) {
            return [
                'id' => $source['id'],
                'name' => $source['name'],
            ];
        })->toArray();
        }

        return [];
    }
}
