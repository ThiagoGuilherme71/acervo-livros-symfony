<?php

namespace App\Service;

use App\Entity\Assunto;
use App\Exception\AssuntoDuplicadoException;
use App\Exception\AssuntoPossuiLivroVinculadoException;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;

class AssuntoService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AssuntoRepository $assuntoRepository,
    ) {
    }

    public function criar(string $descricao): Assunto
    {
        $this->garantirDescricaoUnica($descricao);

        $assunto = new Assunto();
        $assunto->setDescricao($descricao);

        $this->entityManager->persist($assunto);
        $this->entityManager->flush();

        return $assunto;
    }

    public function atualizar(Assunto $assunto, string $descricao): Assunto
    {
        $this->garantirDescricaoUnica($descricao, $assunto);

        $assunto->setDescricao($descricao);
        $this->entityManager->flush();

        return $assunto;
    }

    public function excluir(Assunto $assunto): void
    {
        if (!$assunto->getLivros()->isEmpty()) {
            throw new AssuntoPossuiLivroVinculadoException($assunto->getDescricao());
        }

        $this->entityManager->remove($assunto);
        $this->entityManager->flush();
    }

    private function garantirDescricaoUnica(string $descricao, ?Assunto $assuntoAtual = null): void
    {
        $existente = $this->assuntoRepository->findOneBy(['descricao' => $descricao]);

        if ($existente !== null && $existente !== $assuntoAtual) {
            throw new AssuntoDuplicadoException($descricao);
        }
    }
}