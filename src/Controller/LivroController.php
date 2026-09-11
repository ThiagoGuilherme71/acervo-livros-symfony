<?php

namespace App\Controller;

use App\Entity\Livro;
use App\Exception\LivroSemAutorException;
use App\Exception\LivroValorInvalidoException;
use App\Form\LivroType;
use App\Repository\LivroRepository;
use App\Service\LivroService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livros')]
class LivroController extends AbstractController
{
    public function __construct(
        private readonly LivroService $livroService,
    ) {
    }

    #[Route('', name: 'app_livro_index', methods: ['GET'])]
    public function index(LivroRepository $livroRepository): Response
    {
        return $this->render('livro/index.html.twig', [
            'livros' => $livroRepository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_livro_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $livro = new Livro();
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->livroService->criar($livro);

                $this->addFlash('success', 'Livro cadastrado com sucesso.');

                return $this->redirectToRoute('app_livro_index');
            } catch (LivroSemAutorException|LivroValorInvalidoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('livro/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editar', name: 'app_livro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livro $livro): Response
    {
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->livroService->atualizar($livro);

                $this->addFlash('success', 'Livro atualizado com sucesso.');

                return $this->redirectToRoute('app_livro_index');
            } catch (LivroSemAutorException|LivroValorInvalidoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('livro/edit.html.twig', [
            'form' => $form,
            'livro' => $livro,
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_livro_delete', methods: ['POST'])]
    public function delete(Request $request, Livro $livro): Response
    {
        if ($this->isCsrfTokenValid('delete-livro-' . $livro->getId(), $request->request->get('_token'))) {
            $this->livroService->excluir($livro);
            $this->addFlash('success', 'Livro excluído com sucesso.');
        }

        return $this->redirectToRoute('app_livro_index');
    }
}