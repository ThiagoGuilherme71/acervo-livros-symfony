<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Exception\AssuntoDuplicadoException;
use App\Exception\AssuntoPossuiLivroVinculadoException;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use App\Service\AssuntoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assuntos')]
class AssuntoController extends AbstractController
{
    public function __construct(
        private readonly AssuntoService $assuntoService,
    ) {
    }

    #[Route('', name: 'app_assunto_index', methods: ['GET'])]
    public function index(AssuntoRepository $assuntoRepository): Response
    {
        return $this->render('assunto/index.html.twig', [
            'assuntos' => $assuntoRepository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_assunto_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $assunto = new Assunto();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->assuntoService->criar($assunto->getDescricao());
                $this->addFlash('success', 'Assunto cadastrado com sucesso.');

                return $this->redirectToRoute('app_assunto_index');
            } catch (AssuntoDuplicadoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('assunto/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editar', name: 'app_assunto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Assunto $assunto): Response
    {
        $descricaoOriginal = $assunto->getDescricao();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->assuntoService->atualizar($assunto, $assunto->getDescricao());
                $this->addFlash('success', 'Assunto atualizado com sucesso.');

                return $this->redirectToRoute('app_assunto_index');
            } catch (AssuntoDuplicadoException $e) {
                $assunto->setDescricao($descricaoOriginal);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('assunto/edit.html.twig', [
            'form' => $form,
            'assunto' => $assunto,
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_assunto_delete', methods: ['POST'])]
    public function delete(Request $request, Assunto $assunto): Response
    {
        if ($this->isCsrfTokenValid('delete-assunto-' . $assunto->getId(), $request->request->get('_token'))) {
            try {
                $this->assuntoService->excluir($assunto);
                $this->addFlash('success', 'Assunto excluído com sucesso.');
            } catch (AssuntoPossuiLivroVinculadoException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_assunto_index');
    }
}