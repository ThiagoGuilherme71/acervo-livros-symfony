<?php

namespace App\Tests\Service;

use App\Entity\Assunto;
use App\Entity\Livro;
use App\Exception\AssuntoDuplicadoException;
use App\Exception\AssuntoPossuiLivroVinculadoException;
use App\Repository\AssuntoRepository;
use App\Service\AssuntoService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class AssuntoServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AssuntoRepository $assuntoRepository;
    private AssuntoService $assuntoService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->assuntoRepository = $this->createMock(AssuntoRepository::class);

        $this->assuntoService = new AssuntoService(
            $this->entityManager,
            $this->assuntoRepository
        );
    }

    public function testCriarComDescricaoUnicaDevePersistirERetornarAssunto(): void
    {
        $this->assuntoRepository
            ->method('findOneByDescricaoIgnorandoCaixa')
            ->with('Ficção')
            ->willReturn(null);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $assunto = $this->assuntoService->criar('Ficção');

        $this->assertSame('Ficção', $assunto->getDescricao());
    }

    public function testCriarComDescricaoDuplicadaDeveLancarExcecao(): void
    {
        $assuntoExistente = new Assunto();
        $assuntoExistente->setDescricao('Ficção');

        $this->assuntoRepository
            ->method('findOneByDescricaoIgnorandoCaixa')
            ->willReturn($assuntoExistente);

        $this->entityManager->expects($this->never())->method('persist');

        $this->expectException(AssuntoDuplicadoException::class);

        $this->assuntoService->criar('Ficção');
    }

    public function testAtualizarSemAlterarDescricaoNaoDeveLancarExcecao(): void
    {
        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');

        $this->assuntoRepository
            ->method('findOneByDescricaoIgnorandoCaixa')
            ->willReturn($assunto);

        $this->entityManager->expects($this->once())->method('flush');

        $resultado = $this->assuntoService->atualizar($assunto, 'Ficção');

        $this->assertSame('Ficção', $resultado->getDescricao());
    }

    public function testAtualizarComDescricaoJaUsadaPorOutroAssuntoDeveLancarExcecao(): void
    {
        $assuntoAtual = new Assunto();
        $assuntoAtual->setDescricao('Ficção');

        $outroAssunto = new Assunto();
        $outroAssunto->setDescricao('Terror');

        $this->assuntoRepository
            ->method('findOneByDescricaoIgnorandoCaixa')
            ->willReturn($outroAssunto);

        $this->expectException(AssuntoDuplicadoException::class);

        $this->assuntoService->atualizar($assuntoAtual, 'Terror');
    }

    public function testExcluirAssuntoSemLivroDeveRemover(): void
    {
        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');

        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');

        $this->assuntoService->excluir($assunto);
    }

    public function testExcluirAssuntoComLivroVinculadoDeveLancarExcecao(): void
    {
        $assunto = new Assunto();
        $assunto->setDescricao('Ficção');

        $livro = new Livro();
        $assunto->addLivro($livro);

        $this->entityManager->expects($this->never())->method('remove');

        $this->expectException(AssuntoPossuiLivroVinculadoException::class);

        $this->assuntoService->excluir($assunto);
    }

    public function testCriarComViolacaoDeUnicidadeNoBancoDeveLancarExcecaoDeDominio(): void
    {
        $this->assuntoRepository
            ->method('findOneByDescricaoIgnorandoCaixa')
            ->willReturn(null);

        $this->entityManager
            ->method('flush')
            ->willThrowException($this->createStub(UniqueConstraintViolationException::class));

        $this->expectException(AssuntoDuplicadoException::class);

        $this->assuntoService->criar('Ficção');
    }
}