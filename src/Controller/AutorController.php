<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Exception\AutorDuplicadoException;
use App\Exception\AutorPossuiLivroVinculadoException;
use App\Form\AutorType;
use App\Repository\AutorRepository;
use App\Service\AutorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/autores')]
class AutorController extends AbstractController
{
    public function __construct(
        private readonly AutorService $autorService,
    ) {
    }

    #[Route('', name: 'app_autor_index', methods: ['GET'])]
    public function index(AutorRepository $autorRepository): Response
    {
        return $this->render('autor/index.html.twig', [
            'autores' => $autorRepository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_autor_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->autorService->criar($autor->getNome());
                $this->addFlash('success', 'Autor cadastrado com sucesso.');

                return $this->redirectToRoute('app_autor_index');
            } catch (AutorDuplicadoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('autor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editar', name: 'app_autor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Autor $autor): Response
    {
        $nomeOriginal = $autor->getNome();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->autorService->atualizar($autor, $autor->getNome());
                $this->addFlash('success', 'Autor atualizado com sucesso.');

                return $this->redirectToRoute('app_autor_index');
            } catch (AutorDuplicadoException $e) {
                $autor->setNome($nomeOriginal);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('autor/edit.html.twig', [
            'form' => $form,
            'autor' => $autor,
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_autor_delete', methods: ['POST'])]
    public function delete(Request $request, Autor $autor): Response
    {
        if ($this->isCsrfTokenValid('delete-autor-' . $autor->getId(), $request->request->get('_token'))) {
            try {
                $this->autorService->excluir($autor);
                $this->addFlash('success', 'Autor excluído com sucesso.');
            } catch (AutorPossuiLivroVinculadoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_autor_index');
    }
}