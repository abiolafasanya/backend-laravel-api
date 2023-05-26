<?php

namespace App\Traits;

use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginateArticlesTrait
{
    protected function paginate($items, $perPage)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator(
            $items->forPage($currentPage, $perPage),
            $items->count(),
            $perPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $paginator->appends(request()->query());

        return $paginator;
    }
}


// $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage);