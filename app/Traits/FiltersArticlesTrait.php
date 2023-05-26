<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait FiltersArticlesTrait
{
    protected function applyFilters($articles, Request $request)
    {
        $category = $request->input('category');
        $source = $request->input('source');

        if ($category) {
            $articles = $articles->where('category', $category);
        }

        if ($source) {
            $articles = $articles->where('source', $source);
        }

        return $articles;
    }
}
