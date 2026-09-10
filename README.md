# 📚 Acervo de Livros

Sistema de cadastro de livros, autores e assuntos, desenvolvido em **PHP com Symfony (Twig)**.

---

## 🔗 Links

| Recurso | Link |
|---|---|
| 📦 Repositório | [github.com/ThiagoGuilherme71/acervo-livros-symfony](https://github.com/ThiagoGuilherme71/acervo-livros-symfony) |
| 🐳 Imagem Docker | [hub.docker.com/r/thiagoguilherme71/acervo-livros-symfony](https://hub.docker.com/r/thiagoguilherme71/acervo-livros-symfony) |
| 🎨 Protótipo (Figma) | [Ver no Figma](https://www.figma.com/design/fUSTBVL2G8jUGjwZSeaF05/desafio_acervo?node-id=10-820&t=c4NDuSKufFRvKsW6-1) |

---

## 🛠️ Tecnologias utilizadas

- **PHP 8.2** + **Symfony 7** + **Twig**
- **Doctrine ORM** + **Doctrine Migrations**
- **PostgreSQL 16**
- **Bootstrap 5** (com pequenas customizações em CSS)
- **PHPUnit** (testes unitários e funcionais)
- **Dompdf** (exportação do relatório em PDF)
- **Docker** + **Docker Compose** (Nginx + PHP-FPM + PostgreSQL)
- **GitHub Actions** (CI/CD build e push da imagem para o Docker Hub)

---

## 🚀 Como rodar via Docker (recomendado)

Essa é a forma mais simples: basta ter o **Docker** e o **Docker Compose** instalados. Todo o ambiente (aplicação, banco de dados e Nginx) sobe automaticamente, incluindo a criação do banco e a execução das migrations.

### Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Passo a passo

1. Clone o repositório:
   ```bash
   git clone https://github.com/ThiagoGuilherme71/acervo-livros-symfony.git
   cd acervo-livros-symfony
   ```

2. Suba os containers:
   ```bash
   docker compose up -d --build
   ```

   Isso vai:
   - Buildar a imagem da aplicação PHP
   - Subir o PostgreSQL, o Nginx e o Mailpit
   - Criar o banco de dados e rodar as migrations automaticamente (via `entrypoint.sh`)

3. Acesse a aplicação no navegador:
   ```
   http://localhost:8088
   ```

### Comandos úteis

```bash
# Ver logs da aplicação
docker compose logs -f app

# Rodar comandos do Symfony dentro do container
docker compose exec app php bin/console <comando>

# Rodar os testes
docker compose exec app php bin/phpunit

# Parar os containers
docker compose down

# Parar e remover volumes (banco de dados será apagado)
docker compose down -v
```

---

## 🖥️ Como rodar via servidor local do Symfony

Alternativa para quem já tem o ambiente PHP configurado localmente e prefere não usar Docker.

### Pré-requisitos

- **PHP 8.2+** com as extensões `pdo_pgsql`, `intl`, `zip`
- **Composer**
- **PostgreSQL 16+** instalado localmente
- [Symfony CLI](https://symfony.com/download) (opcional, mas recomendado)

### Passo a passo

1. Clone o repositório:
   ```bash
   git clone https://github.com/ThiagoGuilherme71/acervo-livros-symfony.git
   cd acervo-livros-symfony
   ```

2. Instale as dependências:
   ```bash
   composer install
   ```

3. Configure a conexão com o banco de dados. Crie o arquivo `.env.local` na raiz do projeto:
   ```dotenv
   DATABASE_URL="postgresql://SEU_USUARIO:SUA_SENHA@127.0.0.1:5432/acervo_livros?serverVersion=16&charset=utf8"
   ```
   > Troque `SEU_USUARIO` e `SUA_SENHA` pelas credenciais do seu Postgres local.

4. Crie o banco de dados:
   ```bash
   php bin/console doctrine:database:create
   ```

5. Rode as migrations (cria as tabelas e a view do relatório):
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. Suba o servidor:
   ```bash
   symfony server:start
   ```
   Ou, sem o Symfony CLI:
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

7. Acesse no navegador:
   ```
   http://127.0.0.1:8089
   ```

---

## ✅ Rodando os testes

```bash
# Todos os testes
php bin/phpunit

# Apenas testes unitários (Services)
php bin/phpunit tests/Service

# Apenas testes funcionais (Controllers)
php bin/phpunit tests/Controller
```

> Os testes funcionais utilizam um banco de dados separado (`acervo_livros_test`), configurado via `.env.test.local`.

---

## 📊 Funcionalidades

- CRUD completo de **Livros**, **Autores** e **Assuntos**
- Relacionamento N:N entre Livro ↔ Autor e Livro ↔ Assunto
- Validações de negócio (nome/descrição duplicados, exclusão com vínculo, livro sem autor, valor inválido)
- Tratamento de erros específicos via exceptions customizadas e listener centralizado
- Relatório de livros agrupados por autor, com **exportação em PDF**
- Interface responsiva com Bootstrap 5

---

