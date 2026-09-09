<?php

namespace App\Exception;

class LivroValorInvalidoException extends \DomainException
{
    public function __construct(string $valor)
    {
        parent::__construct("O valor '{$valor}' informado para o livro é inválido. O valor deve ser maior que zero.");
    }
}