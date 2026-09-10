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
    ): Response {
        $resumoLivros = $livroRepository->contarEValorTotal();

        return $this->render('home/index.html.twig', [
            'totalLivros' => $resumoLivros['total'],
            'totalAutores' => $autorRepository->count([]),
            'totalAssuntos' => $assuntoRepository->count([]),
            'valorTotal' => $resumoLivros['valorTotal'],
        ]);
    }
}