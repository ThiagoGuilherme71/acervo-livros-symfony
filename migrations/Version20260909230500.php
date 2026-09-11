<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260909230500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria view vs_relatorio_livros para o relatório de livros';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW vs_relatorio_livros AS
            SELECT
                a.codau AS autor_id,
                a.nome AS autor_nome,
                l.codl AS livro_id,
                l.titulo AS livro_titulo,
                l.editora AS livro_editora,
                l.edicao AS livro_edicao,
                l.anopublicacao AS livro_ano_publicacao,
                l.valor AS livro_valor,
                ass.codas AS assunto_id,
                ass.descricao AS assunto_descricao
            FROM autor a
            INNER JOIN livro_autor la ON la.autor_codau = a.codau
            INNER JOIN livro l ON l.codl = la.livro_codl
            LEFT JOIN livro_assunto lass ON lass.livro_codl = l.codl
            LEFT JOIN assunto ass ON ass.codas = lass.assunto_codas
            ORDER BY a.nome, l.titulo
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW vs_relatorio_livros');
    }
}