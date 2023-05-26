<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait SearchesArticlesTrait
{
    protected function searchArticles($articles, $keywords)
    {
        return $articles->filter(function ($article) use ($keywords) {
            return stripos($article['title'], $keywords) !== false || stripos($article['content'], $keywords) !== false;
        });
    }
    
}
