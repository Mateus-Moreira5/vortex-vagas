<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected Builder $query;

    public function __construct(protected readonly array $input) {}

    abstract protected function filters(): array;

    public function apply(Builder $query): Builder
    {
        $this->query = $query;

        foreach ($this->filters() as $name) {
            $value = $this->input[$name] ?? null;

            if ($value === null || $value === '' || is_array($value)) {
                continue;
            }

            $this->{Str::camel($name)}(trim((string) $value));
        }

        return $this->query;
    }
}
