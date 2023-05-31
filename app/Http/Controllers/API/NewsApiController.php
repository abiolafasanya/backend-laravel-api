<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\FetchesArticlesTrait;
use App\Traits\FiltersArticlesTrait;
use App\Traits\PaginateArticlesTrait;
use App\Traits\SearchesArticlesTrait;
use App\Http\Controllers\Services\NewsService; // Corrected namespace
use Illuminate\Http\Client\Factory as HttpClient;
use Carbon\Carbon;


class NewsApiController extends Controller
{
    use FetchesArticlesTrait, FiltersArticlesTrait, SearchesArticlesTrait, PaginateArticlesTrait;


    protected $httpClient;
    protected $ttl;
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->ttl = Carbon::now()->addHour(1); // Cache data for 1 hour
    }


    public function index(Request $request)
    {
        $cachedResponse = Cache::remember('articles.index', $this->ttl, function () use ($request) {

            $newsService = new NewsService($this->httpClient);

            $general = $newsService->general($request);
            $nytimes = $newsService->nytimes();
            $guardians = $newsService->guadians($request);
            $headlines = $newsService->headlines($request);

            $result = [
                "message" => 'data retrieved successfully',
                'general' => $general,
                'guardians' => $guardians,
                'nytimes' => $nytimes,
                'headlines' => $headlines,
            ];

            return $result;
        });

        return response()->json($cachedResponse, 200);
    }

    public function headlines(Request $request)
    {
        $newsService = new NewsService($this->httpClient);
        $headlines = $newsService->headlines($request);

        return response()->json($headlines, 200);
    }

    public function getSources() {
        $newsService = new NewsService($this->httpClient);
        $sources = $newsService->fetchSources();

        return response()->json([count($sources), $sources], 200);
    }
}
