<?php

namespace App\Exception;

class VinculoNaoEncontradoException extends \DomainException
{
    public function __construct(string $entidade, string $identificador)
    {
        parent::__construct("O {$entidade} '{$identificador}' não está vinculado a este livro.");
    }
}