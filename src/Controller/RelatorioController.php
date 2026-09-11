<?php

namespace App\Controller;

use App\Service\RelatorioService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relatorio')]
class RelatorioController extends AbstractController
{
    public function __construct(
        private readonly RelatorioService $relatorioService,
    ) {
    }

    #[Route('', name: 'app_relatorio_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('relatorio/index.html.twig', $this->relatorioService->gerarRelatorioPorAutor());
    }

    #[Route('/exportar-pdf', name: 'app_relatorio_pdf', methods: ['GET'])]
    public function exportarPdf(): Response
    {
        $html = $this->renderView('relatorio/pdf.html.twig', $this->relatorioService->gerarRelatorioPorAutor());

        $options = new Options();
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio-acervo.pdf"',
            ]
        );
    }
}