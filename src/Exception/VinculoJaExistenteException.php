<?php

namespace App\Exception;

class VinculoJaExistenteException extends \DomainException
{
    public function __construct(string $entidade, string $identificador)
    {
        parent::__construct("O {$entidade} '{$identificador}' já está vinculado a este livro.");
    }
}