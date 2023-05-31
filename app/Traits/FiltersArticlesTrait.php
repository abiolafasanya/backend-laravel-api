<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait FiltersArticlesTrait
{
    protected function applyFilters($articles, Request $request)
    {
        $category = $request->input('category');
        $source = $request->input('source');
        $publisher = $request->input('author');
        

        if ($category) {
            $articles = $articles->where('category', $category);
        }

        if ($source) {
            $articles = $articles->where('source', $source);
        }


        if ($publisher) {
            $articles = array_filter($articles, function ($article) use ($publisher) {
                return isset($article['publisher']['name']) && $article['publisher']['name'] === $publisher;
            });
        }

        return $articles;
    }


    protected function Filters($articles, Request $request) {
        $category = $request->input('category');
        $source = $request->input('source');
        $publisher = $request->input('publisher.name');

        $collection = collect($articles);

        if ($category) {
            $collection = $collection->where('category', $category);
        }

        if ($source) {
            $collection = $collection->where('source', $source);
        }

        if ($publisher) {
            $collection = $collection->where('publisher.name', $publisher);
        }
        return $collection->toArray();
    }

}


