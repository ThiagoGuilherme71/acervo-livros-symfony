<?php

namespace App\Controller;

use App\Repository\RelatorioLivroRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relatorio')]
class RelatorioController extends AbstractController
{
    #[Route('', name: 'app_relatorio_index', methods: ['GET'])]
    public function index(RelatorioLivroRepository $relatorioLivroRepository): Response
    {
        return $this->render('relatorio/index.html.twig', [
            'agrupadoPorAutor' => $this->agruparPorAutor($relatorioLivroRepository),
        ]);
    }

    #[Route('/exportar-pdf', name: 'app_relatorio_pdf', methods: ['GET'])]
    public function exportarPdf(RelatorioLivroRepository $relatorioLivroRepository): Response
    {
        $html = $this->renderView('relatorio/pdf.html.twig', [
            'agrupadoPorAutor' => $this->agruparPorAutor($relatorioLivroRepository),
        ]);

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

    private function agruparPorAutor(RelatorioLivroRepository $relatorioLivroRepository): array
    {
        $linhas = $relatorioLivroRepository->findAllAgrupadoPorAutor();

        $agrupadoPorAutor = [];

        foreach ($linhas as $linha) {
            $autorId = $linha->autorId;

            if (!isset($agrupadoPorAutor[$autorId])) {
                $agrupadoPorAutor[$autorId] = [
                    'nome' => $linha->autorNome,
                    'livros' => [],
                ];
            }

            $livroId = $linha->livroId;

            if (!isset($agrupadoPorAutor[$autorId]['livros'][$livroId])) {
                $agrupadoPorAutor[$autorId]['livros'][$livroId] = [
                    'titulo' => $linha->livroTitulo,
                    'editora' => $linha->livroEditora,
                    'edicao' => $linha->livroEdicao,
                    'anoPublicacao' => $linha->livroAnoPublicacao,
                    'valor' => $linha->livroValor,
                    'assuntos' => [],
                ];
            }

            if ($linha->assuntoDescricao !== null) {
                $agrupadoPorAutor[$autorId]['livros'][$livroId]['assuntos'][] = $linha->assuntoDescricao;
            }
        }

        return $agrupadoPorAutor;
    }
}