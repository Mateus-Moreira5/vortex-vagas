<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HandlesPagination
{
    public const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public const DEFAULT_PER_PAGE = 20;

    protected function perPage(Request $request): int
    {
        $perPage = $request->integer('per_page', self::DEFAULT_PER_PAGE);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : self::DEFAULT_PER_PAGE;
    }
}
