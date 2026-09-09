<?php

namespace App\Exception;

class AutorPossuiLivroVinculadoException extends \DomainException
{
    public function __construct(string $nome)
    {
        parent::__construct("Não é possível excluir o autor '{$nome}' pois ele possui livro(s) vinculado(s).");
    }
}