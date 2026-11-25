<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

trait RedirectsToIndex
{
    protected function redirectToIndex(string $indexRoute): RedirectResponse
    {
        $previous = url()->previous();
        $index = route($indexRoute);

        return redirect()->to(Str::before($previous, '?') === $index ? $previous : $index);
    }
}
