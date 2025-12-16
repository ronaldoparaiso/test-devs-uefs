# API RESTful com Laravel, MySQL e Docker

Este projeto implementa uma API RESTful completa com funcionalidades de CRUD (Create, Read, Update, Delete) para **Usuários**, **Posts** e **Tags**, seguindo as regras de relacionamento especificadas.

## Arquitetura e Estrutura do Projeto

O projeto é construído sobre o framework **Laravel 8+** e utiliza o **MySQL** como banco de dados. A execução é totalmente orquestrada pelo **Docker** e **Docker Compose**, garantindo um ambiente de desenvolvimento isolado e replicável.

### Estrutura de Relacionamentos (Eloquent ORM)

| Entidade | Relacionamento | Regra |
| :--- | :--- | :--- |
| **User** | `hasMany(Post::class)` | Um usuário pode ter várias postagens. |
| **Post** | `belongsTo(User::class)` | Uma postagem pertence a um usuário. |
| **Post** | `belongsToMany(Tag::class)` | Uma postagem pode ter várias tags. |
| **Tag** | `belongsToMany(Post::class)` | Uma tag pode estar em várias postagens. |

### Decisões Técnicas e Particularidades

1.  **Padrão de Rotas `/api/*`**: Todas as rotas da API foram definidas no arquivo `routes/api.php` e utilizam o `Route::apiResource`, garantindo que o prefixo `/api` seja aplicado automaticamente pelo Laravel.
2.  **Controllers Dedicados**: Foram criados `UserController`, `PostController` e `TagController` dentro do namespace `App\Http\Controllers\Api` para isolar a lógica de cada recurso.
3.  **Validação de Dados**: A validação de dados é realizada diretamente nos métodos dos Controllers (`$request->validate()`), retornando respostas JSON com código `422` em caso de falha.
4.  **Relacionamento N:M (Post-Tag)**: O relacionamento muitos-para-muitos entre `Post` e `Tag` é gerenciado pela tabela pivô `post_tag`, conforme o padrão do Laravel. A sincronização de tags é feita nos métodos `store` e `update` do `PostController` usando `attach` e `sync`.

## Como Rodar o Projeto Localmente (Docker)

### Pré-requisitos

*   Docker
*   Docker Compose

### Passos para Execução

1.  **Clonar o Repositório** (Simulação, pois o código já está no diretório `api-rest-laravel`):
    ```bash
    cd api-rest-laravel
    ```

2.  **Construir e Iniciar os Contêineres**:
    O `docker-compose.yml` irá construir a imagem da aplicação (baseada no `Dockerfile`) e iniciar o contêiner do MySQL.
    ```bash
    docker-compose up -d --build
    ```

3.  **Instalar Dependências e Gerar Chave (Dentro do Contêiner)**:
    A instalação do Composer já está no `Dockerfile`, mas a chave da aplicação precisa ser gerada.
    ```bash
    docker exec -it laravel-api-rest-app php artisan key:generate
    ```

4.  **Executar as Migrações do Banco de Dados**:
    Isso criará as tabelas `users`, `posts`, `tags` e `post_tag` no banco de dados MySQL.
    ```bash
    docker exec -it laravel-api-rest-app php artisan migrate
    ```

O projeto estará acessível em `http://localhost:8000`.

## Endpoints da API (Para Teste)

Todos os endpoints utilizam o prefixo `/api`.

| Recurso | Método | Rota | Descrição |
| :--- | :--- | :--- | :--- |
| **Usuários** | `GET` | `/api/users` | Lista todos os usuários (com posts) |
| **Usuários** | `POST` | `/api/users` | Cria um novo usuário |
| **Usuários** | `GET` | `/api/users/{id}` | Exibe um usuário específico (com posts) |
| **Usuários** | `PUT/PATCH` | `/api/users/{id}` | Atualiza um usuário |
| **Usuários** | `DELETE` | `/api/users/{id}` | Deleta um usuário |
| **Posts** | `GET` | `/api/posts` | Lista todos os posts (com usuário e tags) |
| **Posts** | `POST` | `/api/posts` | Cria um novo post (requer `user_id` e opcionalmente `tags[]`) |
| **Posts** | `GET` | `/api/posts/{id}` | Exibe um post específico (com usuário e tags) |
| **Posts** | `PUT/PATCH` | `/api/posts/{id}` | Atualiza um post |
| **Posts** | `DELETE` | `/api/posts/{id}` | Deleta um post |
| **Tags** | `GET` | `/api/tags` | Lista todas as tags (com posts) |
| **Tags** | `POST` | `/api/tags` | Cria uma nova tag |
| **Tags** | `GET` | `/api/tags/{id}` | Exibe uma tag específica (com posts) |
| **Tags** | `PUT/PATCH` | `/api/tags/{id}` | Atualiza uma tag |
| **Tags** | `DELETE` | `/api/tags/{id}` | Deleta uma tag |

### Exemplo de Teste (Criação de Usuário)

**Requisição:** `POST http://localhost:8000/api/users`

**Corpo (JSON):**
```json
{
    "name": "João da Silva",
    "email": "joao@exemplo.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Resposta (201 Created):**
```json
{
    "success": true,
    "data": {
        "name": "João da Silva",
        "email": "joao@exemplo.com",
        "updated_at": "...",
        "created_at": "...",
        "id": 1
    },
    "message": "User created successfully"
}
```
