# URL Shortener

Este é um projeto simples de encurtador de URLs desenvolvido em PHP usando o Slim Framework. Ele permite que os usuários insiram uma URL longa e gerem uma URL curta, que redireciona para a URL original.

## Funcionalidades

- Encurtar URLs longas.
- Redirecionamento usando URLs curtas.
- Registro de logs de acesso às URLs curtas.
- Listagem de urls curtas.
- Gerar QRCode da urls curtas.

## Tecnologias Utilizadas

- **PHP**: Linguagem de programação principal.
- **Slim Framework**: Um micro framework PHP usado para a construção da aplicação.
- **Twig**: Motor de templates utilizado para renderizar as views.
- **PDO**: Interface para acesso ao banco de dados MySQL.
- **Phinx**: Ferramenta de migração de banco de dados.
- **PHP-DI**: Container de injeção de dependências para PHP.
- **dotenv**: Biblioteca para carregar variáveis de ambiente de um arquivo `.env`.
- **qr-code**: Biblioteca que gera o QRCode.

## Requisitos

- **PHP 7.4+**
- **Composer**
- **MySQL**

## Instalação

1. Clone o repositório:
    ```bash
    git clone https://github.com/yurineves92/url-shortener.git
    cd url-shortener
    ```

2. Instale as dependências via Composer:
    ```bash
    composer install
    ```

3. Crie um banco de dados MySQL e configure o arquivo `.env`:
    ```bash
    cp .env.example .env
    ```
    Edite o arquivo `.env` com suas credenciais de banco de dados.

4. Execute as migrações para criar as tabelas necessárias:
    ```bash
    vendor/bin/phinx migrate
    ```

## Uso

Para iniciar o servidor de desenvolvimento, execute:

```bash
composer start
```
A aplicação estará disponível em http://localhost:8080.

## Endpoints

- GET /: Página inicial com o formulário para encurtar uma URL.
- POST /: Encurta a URL enviada no formulário.
- GET /{short_url_path}: Redireciona para a URL original associada ao short_url_path.
- GET /recent-urls: Listagem de urls encurtadas. 

## Estrutura do Projeto

- src/: Contém os arquivos principais da aplicação, incluindo modelos, controladores e configurações.
- public/: Pasta pública do servidor, contendo o index.php que inicia a aplicação.
- migrations/: Diretório de migrações do banco de dados.
- views/: Arquivos Twig para renderização das páginas HTML.
- twig/: Uso da extensão twig dentro do projeto.