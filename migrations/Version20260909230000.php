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
                a.id AS autor_id,
                a.nome AS autor_nome,
                l.id AS livro_id,
                l.titulo AS livro_titulo,
                l.editora AS livro_editora,
                l.edicao AS livro_edicao,
                l.ano_publicacao AS livro_ano_publicacao,
                l.valor AS livro_valor,
                ass.id AS assunto_id,
                ass.descricao AS assunto_descricao
            FROM autor a
            INNER JOIN livro_autor la ON la.autor_id = a.id
            INNER JOIN livro l ON l.id = la.livro_id
            LEFT JOIN livro_assunto lass ON lass.livro_id = l.id
            LEFT JOIN assunto ass ON ass.id = lass.assunto_id
            ORDER BY a.nome, l.titulo
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW vs_relatorio_livros');
    }
}