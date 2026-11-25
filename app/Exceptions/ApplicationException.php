<?php

namespace App\Exceptions;

use DomainException;

class ApplicationException extends DomainException
{
    public static function vacancyPaused(): self
    {
        return new self('Esta vaga está pausada e não recebe novas candidaturas.');
    }

    public static function alreadyApplied(): self
    {
        return new self('O candidato já está inscrito nesta vaga.');
    }
}
