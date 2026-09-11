<?php

namespace App\Repository;

use App\Entity\RelatorioLivro;
use Doctrine\DBAL\Connection;

class RelatorioLivroRepository
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * @return RelatorioLivro[]
     */
    public function findAllAgrupadoPorAutor(): array
    {
        // Lê direto a view via SQL nativo porque assunto_id é nullable e quebraria a PK composta que o doctrine exige.

        $sql = 'SELECT * FROM vs_relatorio_livros ORDER BY autor_nome, livro_titulo';

        $rows = $this->connection->fetchAllAssociative($sql);

        return array_map(fn (array $row) => RelatorioLivro::fromArray($row), $rows);
    }
}