<?php

namespace App\Exception;

class AssuntoPossuiLivroVinculadoException extends \DomainException
{
    public function __construct(string $descricao)
    {
        parent::__construct("Não é possível excluir o assunto '{$descricao}' pois ele possui livro(s) vinculado(s).");
    }
}