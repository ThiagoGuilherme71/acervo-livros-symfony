<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AutorControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $conexao = $this->entityManager->getConnection();
        $conexao->executeStatement('DELETE FROM livro_autor');
        $conexao->executeStatement('DELETE FROM autor');
    }

    public function testCriarAutorComSucesso(): void
    {
        $this->client->request('GET', '/autores/novo');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Salvar', [
            'autor[nome]' => 'Machado de Assis',
        ]);

        $this->assertResponseRedirects('/autores');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('body', 'Machado de Assis');
        $this->assertSelectorTextContains('.alert-success', 'Autor cadastrado com sucesso.');
    }
}