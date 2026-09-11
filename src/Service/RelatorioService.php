<?php

namespace App\Service;

use App\Repository\RelatorioLivroRepository;

class RelatorioService
{
    public function __construct(
        private readonly RelatorioLivroRepository $relatorioLivroRepository,
    ) {
    }

    public function gerarRelatorioPorAutor(): array
    {
        $linhas = $this->relatorioLivroRepository->findAllAgrupadoPorAutor();

        $agrupadoPorAutor = [];
        $livrosContabilizados = [];
        $totalGeral = 0.0;

        foreach ($linhas as $linha) {
            $autorId = $linha->autorId;

            if (!isset($agrupadoPorAutor[$autorId])) {
                $agrupadoPorAutor[$autorId] = [
                    'nome' => $linha->autorNome,
                    'livros' => [],
                    'subtotal' => 0.0,
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

                $agrupadoPorAutor[$autorId]['subtotal'] += (float) $linha->livroValor;
            }

            if (!isset($livrosContabilizados[$livroId])) {
                $livrosContabilizados[$livroId] = true;
                $totalGeral += (float) $linha->livroValor;
            }

            if ($linha->assuntoDescricao !== null) {
                $agrupadoPorAutor[$autorId]['livros'][$livroId]['assuntos'][] = $linha->assuntoDescricao;
            }
        }

        return [
            'agrupadoPorAutor' => $agrupadoPorAutor,
            'totalGeral' => $totalGeral,
            'geradoEm' => new \DateTimeImmutable(),
        ];
    }
}