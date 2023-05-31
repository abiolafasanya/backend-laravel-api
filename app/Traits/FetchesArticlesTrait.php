<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use App\Exceptions\ApiRequestException;
use Illuminate\Http\Client\Factory as HttpClient;

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

        // check if $urls is an array or a string
        if (is_array($urls)) {
            foreach ($urls as $url) {
                // process each url in the array
                $this->processUrl($url, $articles, $token);
            }
        } else {
            // process the single url string
            $this->processUrl($urls, $articles, $token);
        }

        return $articles;
    }

    private function processUrl(string $url, Collection &$articles, string $token): void
    {
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
            $err = 'Unexpected error occurred: ' . $e->getMessage() . ', Code: ' . $e->getCode();
            // throw new Throwable($err);
            throw new ApiRequestException($err);
        }
    }


    protected function fetchHeadlines($url, $headers, $query)
    {
        $client = new Client();

        $response = $client->request('GET', $url, [
            'headers' => $headers,
            'query' => $query,
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
