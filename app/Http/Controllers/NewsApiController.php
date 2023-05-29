<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\FetchesArticlesTrait;
use App\Traits\FiltersArticlesTrait;
use App\Traits\PaginateArticlesTrait;
use App\Traits\SearchesArticlesTrait;
use App\Http\Resources\NewsApiResource;
use Illuminate\Http\Client\Factory as HttpClient;

class NewsApiController extends Controller
{
    use FetchesArticlesTrait, FiltersArticlesTrait, SearchesArticlesTrait, PaginateArticlesTrait;

    protected $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Fetches and returns a paginated list of articles.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Request $request)
    {
        $urls = [
            'https://newsapi.org/v2/everything?domains=wsj.com',
            'https://newsapi.org/v2/top-headlines?country=ng',
            'https://newsapi.org/v2/top-headlines?country=us',
            'https://newsapi.org/v2/top-headlines?category=sports&language=en',
            'https://newsapi.org/v2/top-headlines?sources=techcrunch',
            //'https://newsapi.org/v2/top-headlines?country=us&category=business',
        ];

        $token = config('articles.newsapi.key');
        $articles = $this->fetchArticles($urls, $token);

        $filteredArticles = $this->applyFilters($articles, $request);

        $perPage = intval($request->query('perPage', 20)); // using null coalescing operator

        $paginator = $this->paginate(collect($filteredArticles), $perPage);

        $newsResource = NewsApiResource::collection($paginator);

        return $newsResource->response()->setStatusCode(200);
    }

    /**
     * Searches for articles based on the provided keywords and returns a paginated list of matching articles.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keywords' => 'required|string',
            'page' => 'nullable|integer|min:1',
        ]);

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
        return $newsResource->response()->setStatusCode(200);
    }

    /**
     * Updates the user preferences with the provided data.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function preferences(Request $request)
    {
        $validatedData = $request->validate([
            'sources' => 'nullable|array',
            'categories' => 'nullable|array',
            'authors' => 'nullable|array',
        ]);

        $user = $request->user();
        $user->preferred_sources = $validatedData['sources'] ?? [];
        $user->preferred_categories = $validatedData['categories'] ?? [];
        $user->preferred_authors = $validatedData['authors'] ?? [];
        $user->save();

        return response()->json([
            'message' => 'User preferences updated successfully.',
        ], 200);
    }

    public function headlines(Request $request)
    {
        $query = '?country=us&category=business';
        $url = 'https://newsapi.org/v2/top-headlines'.$query;
        $token = config('articles.newsapi.key');
        $articles = $this->fetchArticles($url, $token);
        $filteredArticles = $this->applyFilters($articles, $request);

        $perPage = 6;
        $paginator = $this->paginate(collect($filteredArticles), $perPage);
        $newsResource = NewsApiResource::collection($paginator);
        return $newsResource->response()->setStatusCode(200);
    }
}
