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

    private function criarLivroValido(): Livro
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $livro = new Livro();
        $livro->setTitulo('Dom Casmurro');
        $livro->setEditora('Editora X');
        $livro->setEdicao(1);
        $livro->setAnoPublicacao('1899');
        $livro->setValor('39.90');
        $livro->addAutor($autor);

        return $livro;
    }

    public function testCriarComDadosValidosDevePersistir(): void
    {
        $livro = $this->criarLivroValido();

        $this->entityManager->expects($this->once())->method('persist')->with($livro);
        $this->entityManager->expects($this->once())->method('flush');

        $resultado = $this->livroService->criar($livro);

        $this->assertSame($livro, $resultado);
    }

    public function testCriarSemAutorDeveLancarExcecao(): void
    {
        $livro = $this->criarLivroValido();
        $livro->removeAutor($livro->getAutores()->first());

        $this->entityManager->expects($this->never())->method('persist');

        $this->expectException(LivroSemAutorException::class);

        $this->livroService->criar($livro);
    }

    public function testCriarComValorZeroDeveLancarExcecao(): void
    {
        $livro = $this->criarLivroValido();
        $livro->setValor('0');

        $this->entityManager->expects($this->never())->method('persist');

        $this->expectException(LivroValorInvalidoException::class);

        $this->livroService->criar($livro);
    }

    public function testCriarComValorNegativoDeveLancarExcecao(): void
    {
        $livro = $this->criarLivroValido();
        $livro->setValor('-10.00');

        $this->expectException(LivroValorInvalidoException::class);

        $this->livroService->criar($livro);
    }

    public function testAtualizarComDadosValidosDeveDarFlush(): void
    {
        $livro = $this->criarLivroValido();

        $this->entityManager->expects($this->once())->method('flush');
        $this->entityManager->expects($this->never())->method('persist');

        $resultado = $this->livroService->atualizar($livro);

        $this->assertSame($livro, $resultado);
    }

    public function testAtualizarSemAutorDeveLancarExcecao(): void
    {
        $livro = $this->criarLivroValido();
        $livro->removeAutor($livro->getAutores()->first());

        $this->expectException(LivroSemAutorException::class);

        $this->livroService->atualizar($livro);
    }

    public function testCriarComAssuntosDeveManterTodos(): void
    {
        $livro = $this->criarLivroValido();

        $assunto1 = new Assunto();
        $assunto1->setDescricao('Ficção');
        $assunto2 = new Assunto();
        $assunto2->setDescricao('Romance');

        $livro->addAssunto($assunto1);
        $livro->addAssunto($assunto2);

        $resultado = $this->livroService->criar($livro);

        $this->assertCount(2, $resultado->getAssuntos());
    }

    public function testExcluirDeveRemoverLivro(): void
    {
        $livro = $this->criarLivroValido();

        $this->entityManager->expects($this->once())->method('remove')->with($livro);
        $this->entityManager->expects($this->once())->method('flush');

        $this->livroService->excluir($livro);
    }
}