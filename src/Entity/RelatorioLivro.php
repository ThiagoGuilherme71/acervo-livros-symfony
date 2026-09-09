<?php

namespace App\Entity;

// Lê direto a view via SQL nativo porque assunto_id é nullable e quebraria a PK composta que o doctrine exige.

final class RelatorioLivro
{
    public function __construct(
        public readonly int $autorId,
        public readonly string $autorNome,
        public readonly int $livroId,
        public readonly string $livroTitulo,
        public readonly string $livroEditora,
        public readonly int $livroEdicao,
        public readonly string $livroAnoPublicacao,
        public readonly string $livroValor,
        public readonly ?int $assuntoId,
        public readonly ?string $assuntoDescricao,
    ) {
    }

    public static function fromArray(array $row): self
    {
        return new self(
            autorId:            (int) $row['autor_id'],
            autorNome:          $row['autor_nome'],
            livroId:            (int) $row['livro_id'],
            livroTitulo:        $row['livro_titulo'],
            livroEditora:       $row['livro_editora'],
            livroEdicao:        (int) $row['livro_edicao'],
            livroAnoPublicacao: $row['livro_ano_publicacao'],
            livroValor:         $row['livro_valor'],
            assuntoId:          $row['assunto_id'] !== null ? (int) $row['assunto_id'] : null,
            assuntoDescricao:   $row['assunto_descricao'],
        );
    }
}