<?php

namespace App\Controller;

use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\LivroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        LivroRepository $livroRepository,
        AutorRepository $autorRepository,
        AssuntoRepository $assuntoRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $livros = $livroRepository->findAll();

        $valorTotal = array_reduce(
            $livros,
            fn (float $total, $livro) => $total + (float) $livro->getValor(),
            0.0
        );

        return $this->render('home/index.html.twig', [
            'totalLivros' => count($livros),
            'totalAutores' => count($autorRepository->findAll()),
            'totalAssuntos' => count($assuntoRepository->findAll()),
            'valorTotal' => $valorTotal,
        ]);
    }
}