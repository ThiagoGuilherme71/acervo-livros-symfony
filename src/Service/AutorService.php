<?php

namespace App\Service;

use App\Entity\Autor;
use App\Exception\AutorDuplicadoException;
use App\Exception\AutorPossuiLivroVinculadoException;
use App\Repository\AutorRepository;
use Doctrine\ORM\EntityManagerInterface;

class AutorService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AutorRepository $autorRepository,
    ) {
    }

    public function criar(string $nome): Autor
    {
        $this->garantirNomeUnico($nome);

        $autor = new Autor();
        $autor->setNome($nome);

        $this->entityManager->persist($autor);
        $this->entityManager->flush();

        return $autor;
    }

    public function atualizar(Autor $autor, string $nome): Autor
    {
        $this->garantirNomeUnico($nome, $autor);

        $autor->setNome($nome);
        $this->entityManager->flush();

        return $autor;
    }

    public function excluir(Autor $autor): void
    {
        if (!$autor->getLivros()->isEmpty()) {
            throw new AutorPossuiLivroVinculadoException($autor->getNome());
        }

        $this->entityManager->remove($autor);
        $this->entityManager->flush();
    }

    private function garantirNomeUnico(string $nome, ?Autor $autorAtual = null): void
    {
        $existente = $this->autorRepository->findOneBy(['nome' => $nome]);

        if ($existente !== null && $existente !== $autorAtual) {
            throw new AutorDuplicadoException($nome);
        }
    }
}