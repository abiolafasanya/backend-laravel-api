<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OtherNewsController extends Controller
{
    //

    public function index()
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
        return response()->json($response);
    }

    public function guardians(Request $request)
    {
        $url = 'https://content.guardianapis.com/search';
        $response = Http::get($url, [
            'api-key' => config('articles.guardians.key'),
            'q' => $request->query('q', 'film'),
            ]);
            
            $articles = collect($response->json()['response']['results'])->map(function ($article) {
            $unsplashUrl = 'https://api.unsplash.com/search/photos';
            $imageResponse = Http::withHeaders([
            'Authorization' => 'Client-ID '. config('articles.unsplash.key'),
        ])->get($unsplashUrl, [
            'query' => $article['webTitle'],
        ]);
        
        $imageData = $imageResponse->json()['results'][0] ?? null;
            return [
                'title' => $article['webTitle'],
                'id' => $article['id'],
                'image' => $imageData ? $imageData['urls']['regular'] : null, // $article['fields']['thumbnail'] ?? null,
                'description' => $article['fields']['trailText'] ?? null,
                'publishedAt' => $article['webPublicationDate'],
                'rating' => $article['fields']['starRating'] ?? null,
                'source' => $article['webUrl'],
            ];
        });
        return response()->json($articles);
    }


}
