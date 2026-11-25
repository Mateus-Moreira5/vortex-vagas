<?php

namespace Tests\Unit;

use App\Enums\VacancyType;
use PHPUnit\Framework\TestCase;

class VacancyTypeTest extends TestCase
{
    public function test_each_type_has_a_readable_label(): void
    {
        $this->assertSame('CLT', VacancyType::Clt->label());
        $this->assertSame('Pessoa Jurídica (PJ)', VacancyType::Pj->label());
        $this->assertSame('Freelancer', VacancyType::Freelancer->label());
    }

    public function test_types_are_created_from_stored_values(): void
    {
        $this->assertSame(VacancyType::Pj, VacancyType::from('pj'));
        $this->assertNull(VacancyType::tryFrom('estagio'));
    }
}
