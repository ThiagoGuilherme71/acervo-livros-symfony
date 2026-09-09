<?php

namespace App\Exception;

class AssuntoDuplicadoException extends \DomainException
{
    public function __construct(string $descricao)
    {
        parent::__construct("Já existe um assunto cadastrado com a descrição '{$descricao}'.");
    }
}