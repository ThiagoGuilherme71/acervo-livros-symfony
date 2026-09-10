<?php

namespace App\Service;

use App\Entity\Assunto;
use App\Entity\Autor;
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

    /**
     * @param Autor[] $autores
     * @param Assunto[] $assuntos
     */
    public function criar(string $titulo, string $editora, int $edicao, string $anoPublicacao, string $valor, array $autores, array $assuntos = []): Livro {
        
        $this->garantirValorValido($valor);

        if (empty($autores)){
            throw new LivroSemAutorException();
        }

        $livro = new Livro();
        $livro->setTitulo($titulo);
        $livro->setEditora($editora);
        $livro->setEdicao($edicao);
        $livro->setAnoPublicacao($anoPublicacao);
        $livro->setValor($valor);

        foreach ($autores as $autor)
            $livro->addAutor($autor);
        

        foreach ($assuntos as $assunto) {
            $livro->addAssunto($assunto);
        }

        $this->entityManager->persist($livro);
        $this->entityManager->flush();

        return $livro;
    }

    /**
     * @param Autor[] $autores
     * @param Assunto[] $assuntos
     */
    public function atualizar( Livro $livro, string $titulo, string $editora, int $edicao, string $anoPublicacao, string $valor, array $autores, array $assuntos = []): Livro {
        
        $this->garantirValorValido($valor);

        if (empty($autores))
            throw new LivroSemAutorException();
        

        $livro->setTitulo($titulo);
        $livro->setEditora($editora);
        $livro->setEdicao($edicao);
        $livro->setAnoPublicacao($anoPublicacao);
        $livro->setValor($valor);

        $this->sincronizarAutores($livro, $autores);
        $this->sincronizarAssuntos($livro, $assuntos);

        $this->entityManager->flush();

        return $livro;
    }

    public function excluir(Livro $livro): void
    {
        $this->entityManager->remove($livro);
        $this->entityManager->flush();
    }

    private function garantirValorValido(string $valor): void
    {
        if ((float) $valor <= 0) {
            throw new LivroValorInvalidoException($valor);
        }
    }

    private function sincronizarAutores(Livro $livro, array $autoresNovos): void
    {
        foreach ($livro->getAutores() as $autorAtual) {
            if (!in_array($autorAtual, $autoresNovos, true)) {
                $livro->removeAutor($autorAtual);
            }
        }

        foreach ($autoresNovos as $autor) {
            if (!$livro->getAutores()->contains($autor)) {
                $livro->addAutor($autor);
            }
        }
    }

    private function sincronizarAssuntos(Livro $livro, array $assuntosNovos): void
    {
        foreach ($livro->getAssuntos() as $assuntoAtual) {
            if (!in_array($assuntoAtual, $assuntosNovos, true)) {
                $livro->removeAssunto($assuntoAtual);
            }
        }

        foreach ($assuntosNovos as $assunto) {
            if (!$livro->getAssuntos()->contains($assunto)) {
                $livro->addAssunto($assunto);
            }
        }
    }
}