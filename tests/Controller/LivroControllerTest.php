<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class LivroControllerTest extends WebTestCase
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
    }

    public function testCriarLivroSemAutorDeveExibirMensagemDeErro(): void
    {
        $crawler = $this->client->request('GET', '/livros/novo');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Salvar livro')->form([
            'livro[titulo]' => 'Dom Casmurro',
            'livro[editora]' => 'Editora X',
            'livro[edicao]' => 1,
            'livro[anoPublicacao]' => '1899',
            'livro[valor]' => '39,90',
        ]);

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('body', 'Selecione ao menos um autor');
    }
}