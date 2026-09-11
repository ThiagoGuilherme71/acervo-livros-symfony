<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AssuntoControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $conexao = $this->entityManager->getConnection();
        $conexao->executeStatement('DELETE FROM livro_assunto');
        $conexao->executeStatement('DELETE FROM assunto');
    }

    public function testCriarAssuntoComSucesso(): void
    {
        $this->client->request('GET', '/assuntos/novo');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Salvar', [
            'assunto[descricao]' => 'Ficção Científica',
        ]);

        $this->assertResponseRedirects('/assuntos');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('body', 'Ficção Científica');
        $this->assertSelectorTextContains('.alert-success', 'Assunto cadastrado com sucesso.');
    }
}