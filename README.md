# Acervo de Livros

Sistema de cadastro de livros, autores e assuntos, desenvolvido em PHP com Symfony e Twig, como parte de um desafio técnico.

## Links

- Repositório: https://github.com/ThiagoGuilherme71/acervo-livros-symfony
- Imagem Docker: https://hub.docker.com/r/thiagoguilherme71/acervo-livros-symfony
- Protótipo (Figma): https://www.figma.com/design/fUSTBVL2G8jUGjwZSeaF05/desafio_acervo?node-id=10-820&t=c4NDuSKufFRvKsW6-1

## Tecnologias

- PHP 8.2, Symfony 7, Twig
- Doctrine ORM e Doctrine Migrations
- PostgreSQL 16
- Bootstrap 5
- PHPUnit (testes unitários e funcionais)
- Dompdf (exportação do relatório em PDF)
- Docker e Docker Compose (Nginx, PHP-FPM e PostgreSQL)
- GitHub Actions (build e push da imagem para o Docker Hub)

## Como rodar via Docker (recomendado)

Requer Docker e Docker Compose instalados.

```bash
git clone https://github.com/ThiagoGuilherme71/acervo-livros-symfony.git
cd acervo-livros-symfony
docker compose up -d --build
```

O container da aplicação cria o banco e roda as migrations automaticamente ao iniciar (via `entrypoint.sh`). A aplicação fica disponível em `http://localhost:8089`.

Comandos úteis:

```bash
docker compose logs -f app
docker compose exec app php bin/console <comando>
docker compose exec app php bin/phpunit
docker compose down
docker compose down -v   # remove também os volumes (apaga os dados do banco)
```

## Como rodar via servidor local do Symfony

Alternativa para quem prefere não usar Docker.

Pré-requisitos: PHP 8.2+ com as extensões `pdo_pgsql`, `intl` e `zip`, Composer, e PostgreSQL 16+ instalado localmente.

```bash
git clone https://github.com/ThiagoGuilherme71/acervo-livros-symfony.git
cd acervo-livros-symfony
composer install
```

Crie o arquivo `.env.local` na raiz do projeto com a conexão do seu banco:

```dotenv
DATABASE_URL="postgresql://SEU_USUARIO:SUA_SENHA@127.0.0.1:5432/acervo_livros?serverVersion=16&charset=utf8"
```

Crie o banco e rode as migrations:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Suba o servidor:

```bash
symfony server:start --port=8089
```

Sem o Symfony CLI:

```bash
php -S 127.0.0.1:8089 -t public
```

A aplicação fica disponível em `http://127.0.0.1:8089`.

## Testes

```bash
php bin/phpunit                    # todos os testes
php bin/phpunit tests/Service      # unitários
php bin/phpunit tests/Controller   # funcionais
```

Os testes funcionais usam um banco separado (`acervo_livros_test`). Antes de rodar pela primeira vez, configure o `.env.test.local`:

```dotenv
DATABASE_URL="postgresql://SEU_USUARIO:SUA_SENHA@127.0.0.1:5432/acervo_livros?serverVersion=16&charset=utf8"
```

E crie/migre esse banco:

```bash
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:migrations:migrate
```

## Funcionalidades

- CRUD de Livros, Autores e Assuntos
- Relacionamento N:N entre Livro-Autor e Livro-Assunto
- Validações de negócio: nome/descrição duplicados, exclusão bloqueada quando há vínculo, livro sem autor, valor inválido
- Exceptions de domínio tratadas via listener centralizado
- Relatório de livros agrupados por autor, com exportação em PDF

## Autor

Thiago Guilherme
