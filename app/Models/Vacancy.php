<?php

namespace App\Models;

use App\Enums\VacancyType;
use App\Models\Concerns\Filterable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'description', 'type', 'location', 'salary', 'is_paused'])]
class Vacancy extends Model
{
    use Filterable, HasFactory;

    protected $attributes = [
        'is_paused' => false,
    ];

    protected function casts(): array
    {
        return [
            'type' => VacancyType::class,
            'salary' => 'decimal:2',
            'is_paused' => 'boolean',
        ];
    }

    protected function formattedSalary(): Attribute
    {
        return Attribute::get(
            fn () => $this->salary === null ? 'A combinar' : 'R$ '.number_format((float) $this->salary, 2, ',', '.')
        );
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'applications')->withTimestamps();
    }

    #[Scope]
    protected function open(Builder $query): void
    {
        $query->where('is_paused', false);
    }

    public function isOpen(): bool
    {
        return ! $this->is_paused;
    }

    public function togglePause(): void
    {
        $this->update(['is_paused' => ! $this->is_paused]);
    }
}
