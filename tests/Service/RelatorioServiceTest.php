<?php

namespace App\Tests\Service;

use App\Entity\RelatorioLivro;
use App\Repository\RelatorioLivroRepository;
use App\Service\RelatorioService;
use PHPUnit\Framework\TestCase;

class RelatorioServiceTest extends TestCase
{
    public function testAgrupaPorAutorComSubtotalETotalGeralSemContarLivroDuplicado(): void
    {
        $linhas = [
            new RelatorioLivro(1, 'Machado de Assis', 10, 'Dom Casmurro', 'Editora X', 1, '1899', '50.00', 1, 'Romance'),
            new RelatorioLivro(1, 'Machado de Assis', 10, 'Dom Casmurro', 'Editora X', 1, '1899', '50.00', 2, 'Ficção'),
            new RelatorioLivro(1, 'Machado de Assis', 11, 'Memórias Póstumas', 'Editora Y', 2, '1881', '30.00', null, null),
            new RelatorioLivro(2, 'Clarice Lispector', 10, 'Dom Casmurro', 'Editora X', 1, '1899', '50.00', 1, 'Romance'),
        ];

        $repository = $this->createMock(RelatorioLivroRepository::class);
        $repository->method('findAllAgrupadoPorAutor')->willReturn($linhas);

        $service = new RelatorioService($repository);

        $resultado = $service->gerarRelatorioPorAutor();

        $this->assertCount(2, $resultado['agrupadoPorAutor']);
        $this->assertSame(80.0, $resultado['agrupadoPorAutor'][1]['subtotal']);
        $this->assertSame(50.0, $resultado['agrupadoPorAutor'][2]['subtotal']);
        $this->assertSame(80.0, $resultado['totalGeral']);
        $this->assertInstanceOf(\DateTimeImmutable::class, $resultado['geradoEm']);
    }
}