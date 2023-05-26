<?php

namespace App\Traits;

use Illuminate\Http\Client\Factory as HttpClient;
use App\Exceptions\ApiRequestException;

trait FetchesArticlesTrait
{

    protected $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Fetches articles from multiple API endpoints and returns a collection of articles.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fetchArticles($urls, $token)
    {
        $articles = collect();

        foreach ($urls as $url) {
            try {
                $response = $this->httpClient->withToken($token)->get($url);

                if ($response->successful()) {
                    $articles = $articles->concat($response['articles'] ?? $response['response']['results'] ?? []);
                } else {
                    // Throw custom exception for API request failure
                    throw new ApiRequestException('API request failed');
                }
            } catch (\Throwable $e) {
                // Handle any other unexpected exceptions
                //throw new Throwable('Unexpected error occurred: ' . $e->getMessage(), 0, $e);
                $err = 'Unexpected error occurred: ' . $e->getMessage() . ', Code: ' . $e->getCode();
                // throw new Throwable($err);
                throw new ApiRequestException($err);
            }
        }

        return $articles;
    }
}
