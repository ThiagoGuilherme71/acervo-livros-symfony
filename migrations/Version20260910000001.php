<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260910000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona índice único (case-insensitive) em autor.nome e assunto.descricao';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX uniq_autor_nome ON autor (LOWER(nome))');
        $this->addSql('CREATE UNIQUE INDEX uniq_assunto_descricao ON assunto (LOWER(descricao))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_autor_nome');
        $this->addSql('DROP INDEX uniq_assunto_descricao');
    }
}