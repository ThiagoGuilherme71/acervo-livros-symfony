<?php

namespace App\Exception;

class AutorDuplicadoException extends \DomainException
{
    public function __construct(string $nome)
    {
        parent::__construct("Já existe um autor cadastrado com o nome '{$nome}'.");
    }
}