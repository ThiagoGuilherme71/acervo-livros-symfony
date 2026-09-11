<?php

namespace App\Service;

use App\Entity\Livro;
use App\Exception\LivroSemAutorException;
use App\Exception\LivroValorInvalidoException;
use Doctrine\ORM\EntityManagerInterface;

class LivroService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function criar(Livro $livro): Livro
    {
        $this->validar($livro);

        $this->entityManager->persist($livro);
        $this->entityManager->flush();

        return $livro;
    }

    public function atualizar(Livro $livro): Livro
    {
        $this->validar($livro);

        $this->entityManager->flush();

        return $livro;
    }

    public function excluir(Livro $livro): void
    {
        $this->entityManager->remove($livro);
        $this->entityManager->flush();
    }

    private function validar(Livro $livro): void
    {
        if ((float) $livro->getValor() <= 0) {
            throw new LivroValorInvalidoException($livro->getValor());
        }

        if ($livro->getAutores()->isEmpty()) {
            throw new LivroSemAutorException();
        }
    }
}