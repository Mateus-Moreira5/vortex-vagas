<?php

namespace App\Filters;

use App\Enums\VacancyType;

class VacancyFilter extends QueryFilter
{
    protected function filters(): array
    {
        return ['title', 'description', 'type', 'location', 'salary_min', 'salary_max', 'status'];
    }

    protected function title(string $value): void
    {
        $this->query->whereLike('title', "%{$value}%");
    }

    protected function description(string $value): void
    {
        $this->query->whereLike('description', "%{$value}%");
    }

    protected function type(string $value): void
    {
        if ($type = VacancyType::tryFrom($value)) {
            $this->query->where('type', $type);
        }
    }

    protected function location(string $value): void
    {
        $this->query->whereLike('location', "%{$value}%");
    }

    protected function salaryMin(string $value): void
    {
        if (is_numeric($value)) {
            $this->query->where('salary', '>=', $value);
        }
    }

    protected function salaryMax(string $value): void
    {
        if (is_numeric($value)) {
            $this->query->where('salary', '<=', $value);
        }
    }

    protected function status(string $value): void
    {
        match ($value) {
            'open' => $this->query->where('is_paused', false),
            'paused' => $this->query->where('is_paused', true),
            default => null,
        };
    }
}
