<?php

namespace App\Tests\Service;

use App\Entity\Autor;
use App\Entity\Livro;
use App\Exception\AutorDuplicadoException;
use App\Exception\AutorPossuiLivroVinculadoException;
use App\Repository\AutorRepository;
use App\Service\AutorService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AutorServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AutorRepository $autorRepository;
    private AutorService $autorService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->autorRepository = $this->createMock(AutorRepository::class);

        $this->autorService = new AutorService(
            $this->entityManager,
            $this->autorRepository
        );
    }

    public function testCriarComNomeUnicoDevePersistirERetornarAutor(): void
    {
        $this->autorRepository
            ->method('findOneBy')
            ->with(['nome' => 'Machado de Assis'])
            ->willReturn(null);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $autor = $this->autorService->criar('Machado de Assis');

        $this->assertSame('Machado de Assis', $autor->getNome());
    }

    public function testCriarComNomeDuplicadoDeveLancarExcecao(): void
    {
        $autorExistente = new Autor();
        $autorExistente->setNome('Machado de Assis');

        $this->autorRepository
            ->method('findOneBy')
            ->willReturn($autorExistente);

        $this->entityManager->expects($this->never())->method('persist');

        $this->expectException(AutorDuplicadoException::class);

        $this->autorService->criar('Machado de Assis');
    }

    public function testAtualizarSemAlterarNomeNaoDeveLancarExcecao(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $this->autorRepository
            ->method('findOneBy')
            ->willReturn($autor);

        $this->entityManager->expects($this->once())->method('flush');

        $resultado = $this->autorService->atualizar($autor, 'Machado de Assis');

        $this->assertSame('Machado de Assis', $resultado->getNome());
    }

    public function testAtualizarComNomeJaUsadoPorOutroAutorDeveLancarExcecao(): void
    {
        $autorAtual = new Autor();
        $autorAtual->setNome('Machado de Assis');

        $outroAutor = new Autor();
        $outroAutor->setNome('Clarice Lispector');

        $this->autorRepository
            ->method('findOneBy')
            ->willReturn($outroAutor);

        $this->expectException(AutorDuplicadoException::class);

        $this->autorService->atualizar($autorAtual, 'Clarice Lispector');
    }

    public function testExcluirAutorSemLivroDeveRemover(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');

        $this->autorService->excluir($autor);
    }

    public function testExcluirAutorComLivroVinculadoDeveLancarExcecao(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $livro = new Livro();
        $autor->addLivro($livro);

        $this->entityManager->expects($this->never())->method('remove');

        $this->expectException(AutorPossuiLivroVinculadoException::class);

        $this->autorService->excluir($autor);
    }
}