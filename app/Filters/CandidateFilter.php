<?php

namespace App\Filters;

use DateTimeImmutable;

class CandidateFilter extends QueryFilter
{
    protected function filters(): array
    {
        return ['name', 'email', 'phone', 'birth_date_from', 'birth_date_to'];
    }

    protected function name(string $value): void
    {
        $this->query->whereLike('name', "%{$value}%");
    }

    protected function email(string $value): void
    {
        $this->query->whereLike('email', "%{$value}%");
    }

    protected function phone(string $value): void
    {
        $this->query->whereLike('phone', "%{$value}%");
    }

    protected function birthDateFrom(string $value): void
    {
        if ($this->isValidDate($value)) {
            $this->query->whereDate('birth_date', '>=', $value);
        }
    }

    protected function birthDateTo(string $value): void
    {
        if ($this->isValidDate($value)) {
            $this->query->whereDate('birth_date', '<=', $value);
        }
    }

    private function isValidDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
