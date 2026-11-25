<?php

namespace App\Enums;

enum VacancyType: string
{
    case Clt = 'clt';
    case Pj = 'pj';
    case Freelancer = 'freelancer';

    public function label(): string
    {
        return match ($this) {
            self::Clt => 'CLT',
            self::Pj => 'Pessoa Jurídica (PJ)',
            self::Freelancer => 'Freelancer',
        };
    }
}
