<?php

namespace App\Tests\Service;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use App\Exception\LivroSemAutorException;
use App\Exception\LivroValorInvalidoException;
use App\Service\LivroService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LivroServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private LivroService $livroService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->livroService = new LivroService($this->entityManager);
    }

    public function testCriarComDadosValidosDevePersistirERetornarLivro(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $livro = $this->livroService->criar(
            'Dom Casmurro',
            'Editora X',
            1,
            '1899',
            '39.90',
            [$autor]
        );

        $this->assertSame('Dom Casmurro', $livro->getTitulo());
        $this->assertTrue($livro->getAutores()->contains($autor));
    }

    public function testCriarSemAutorDeveLancarExcecao(): void
    {
        $this->entityManager->expects($this->never())->method('persist');

        $this->expectException(LivroSemAutorException::class);

        $this->livroService->criar(
            'Dom Casmurro',
            'Editora X',
            1,
            '1899',
            '39.90',
            []
        );
    }

    public function testCriarComValorZeroDeveLancarExcecao(): void
    {
        $autor = new Autor();

        $this->entityManager->expects($this->never())->method('persist');

        $this->expectException(LivroValorInvalidoException::class);

        $this->livroService->criar(
            'Dom Casmurro',
            'Editora X',
            1,
            '1899',
            '0',
            [$autor]
        );
    }

    public function testCriarComValorNegativoDeveLancarExcecao(): void
    {
        $autor = new Autor();

        $this->expectException(LivroValorInvalidoException::class);

        $this->livroService->criar(
            'Dom Casmurro',
            'Editora X',
            1,
            '1899',
            '-10.00',
            [$autor]
        );
    }

    public function testAtualizarDeveSincronizarAutoresRemovendoOsNaoSelecionados(): void
    {
        $autorAntigo = new Autor();
        $autorAntigo->setNome('Autor Antigo');

        $autorNovo = new Autor();
        $autorNovo->setNome('Autor Novo');

        $livro = new Livro();
        $livro->setTitulo('Título Original');
        $livro->addAutor($autorAntigo);

        $this->entityManager->expects($this->once())->method('flush');

        $this->livroService->atualizar(
            $livro,
            'Título Atualizado',
            'Editora X',
            1,
            '2020',
            '50.00',
            [$autorNovo]
        );

        $this->assertFalse($livro->getAutores()->contains($autorAntigo));
        $this->assertTrue($livro->getAutores()->contains($autorNovo));
    }

    public function testAtualizarMantendoMesmoAutorNaoDeveDuplicarNemRemover(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $livro = new Livro();
        $livro->addAutor($autor);

        $this->livroService->atualizar(
            $livro,
            'Novo Título',
            'Editora X',
            1,
            '2020',
            '50.00',
            [$autor]
        );

        $this->assertCount(1, $livro->getAutores());
        $this->assertTrue($livro->getAutores()->contains($autor));
    }

    public function testAtualizarSemAutorDeveLancarExcecao(): void
    {
        $autor = new Autor();
        $livro = new Livro();
        $livro->addAutor($autor);

        $this->expectException(LivroSemAutorException::class);

        $this->livroService->atualizar(
            $livro,
            'Novo Título',
            'Editora X',
            1,
            '2020',
            '50.00',
            []
        );
    }

    public function testCriarComAssuntosDeveVincularTodos(): void
    {
        $autor = new Autor();
        $assunto1 = new Assunto();
        $assunto1->setDescricao('Ficção');
        $assunto2 = new Assunto();
        $assunto2->setDescricao('Romance');

        $livro = $this->livroService->criar(
            'Dom Casmurro',
            'Editora X',
            1,
            '1899',
            '39.90',
            [$autor],
            [$assunto1, $assunto2]
        );

        $this->assertCount(2, $livro->getAssuntos());
    }

    public function testExcluirDeveRemoverLivro(): void
    {
        $livro = new Livro();

        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');

        $this->livroService->excluir($livro);
    }
}