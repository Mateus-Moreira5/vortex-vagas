<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'email', 'phone', 'birth_date'])]
class Candidate extends Model
{
    use Filterable, HasFactory;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function vacancies(): BelongsToMany
    {
        return $this->belongsToMany(Vacancy::class, 'applications')->withTimestamps();
    }

    public function hasAppliedTo(Vacancy $vacancy): bool
    {
        return $this->vacancies()->whereKey($vacancy->getKey())->exists();
    }
}
