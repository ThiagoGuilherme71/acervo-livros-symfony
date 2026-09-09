<?php

namespace App\Exception;

class LivroSemAutorException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('O livro precisa ter pelo menos um autor vinculado.');
    }
}