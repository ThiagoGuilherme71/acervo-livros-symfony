<?php

namespace App\Tests\Controller;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class RelatorioControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $conexao = $this->entityManager->getConnection();
        $conexao->executeStatement('DELETE FROM livro_assunto');
        $conexao->executeStatement('DELETE FROM livro_autor');
        $conexao->executeStatement('DELETE FROM livro');
        $conexao->executeStatement('DELETE FROM autor');
        $conexao->executeStatement('DELETE FROM assunto');
    }

    public function testRelatorioExibeLivroAgrupadoPorAutorComSubtotalETotal(): void
    {
        $autor = new Autor();
        $autor->setNome('Machado de Assis');

        $assunto = new Assunto();
        $assunto->setDescricao('Romance');

        $livro = new Livro();
        $livro->setTitulo('Dom Casmurro');
        $livro->setEditora('Editora X');
        $livro->setEdicao(1);
        $livro->setAnoPublicacao('1899');
        $livro->setValor('39.90');
        $livro->addAutor($autor);
        $livro->addAssunto($assunto);

        $this->entityManager->persist($autor);
        $this->entityManager->persist($assunto);
        $this->entityManager->persist($livro);
        $this->entityManager->flush();

        $this->client->request('GET', '/relatorio');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Machado de Assis');
        $this->assertSelectorTextContains('body', 'Dom Casmurro');
        $this->assertSelectorTextContains('body', 'R$ 39,90');
    }

    public function testExportarPdfRetornaArquivoPdf(): void
    {
        $autor = new Autor();
        $autor->setNome('Clarice Lispector');

        $livro = new Livro();
        $livro->setTitulo('A Hora da Estrela');
        $livro->setEditora('Editora Y');
        $livro->setEdicao(1);
        $livro->setAnoPublicacao('1977');
        $livro->setValor('29.90');
        $livro->addAutor($autor);

        $this->entityManager->persist($autor);
        $this->entityManager->persist($livro);
        $this->entityManager->flush();

        $this->client->request('GET', '/relatorio/exportar-pdf');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/pdf');
    }
}