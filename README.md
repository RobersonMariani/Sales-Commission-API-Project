# Sales Commission API

API RESTful para gerenciamento de vendas e cálculo automático de comissões de vendedores, construída com Laravel 12.

## Funcionalidades

- Autenticação JWT (registro, login, logout, perfil)
- CRUD de vendedores
- Cadastro e listagem de vendas com cálculo automático de comissão (taxa configurável, padrão 8,5%)
- Envio diário de e-mails com resumo de comissões para cada vendedor
- Envio diário de e-mail administrativo com o resumo geral de vendas
- Reenvio manual de e-mail de comissão para um vendedor específico
- Paginação configurável em todas as listagens
- Fila assíncrona via Redis para processamento de e-mails
- Validação de dados via DTOs com Spatie Laravel Data

## Stack

| Camada         | Tecnologia                                 |
|----------------|--------------------------------------------|
| Linguagem      | PHP 8.5                                    |
| Framework      | Laravel 12                                 |
| Banco de Dados | MySQL 9.x                                  |
| Cache / Fila   | Redis                                      |
| Autenticação   | JWT (`php-open-source-saver/jwt-auth`)     |
| DTOs           | Spatie Laravel Data                        |
| E-mail (dev)   | Mailpit                                    |
| Containers     | Docker Compose                             |
| Code Style     | Laravel Pint (PSR-12 strict)               |
| Análise        | PHPStan / Larastan (nível 5)               |
| Testes         | PHPUnit (unitários + integração)           |

## Requisitos

- Docker e Docker Compose

## Instalação

```bash
git clone https://github.com/RobersonMariani/Sales-Commission-API-Project.git
cd Sales-Commission-API-Project

cp .env.example .env

docker compose up -d

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed
```

Após o setup, a API estará disponível em `http://localhost:8000`.

## Serviços

| Serviço   | URL / Porta           | Descrição                              |
|-----------|-----------------------|----------------------------------------|
| API       | http://localhost:8000  | Aplicação Laravel                      |
| Mailpit   | http://localhost:8025  | Interface web para visualizar e-mails  |
| MySQL     | localhost:3306         | Banco de dados                         |
| Redis     | localhost:6380         | Cache e filas                          |

## Dados Iniciais (Seeder)

| Dado        | Detalhe                                                   |
|-------------|-----------------------------------------------------------|
| Admin       | `admin@salescommission.com` / `password`                  |
| Vendedores  | 10 vendedores gerados via factory                         |
| Vendas      | 3 a 8 vendas por vendedor, distribuídas nos últimos 30 dias |

---

## Endpoints

Todas as rotas (exceto registro, login e health check) requerem o header:

```
Authorization: Bearer {token}
```

### Health Check

```
GET /api/health
```

```json
{ "status": "ok" }
```

---

### Autenticação

#### Registrar usuário

```
POST /api/auth/register
```

**Body:**

```json
{
  "name": "João Silva",
  "email": "joao@email.com",
  "password": "12345678",
  "password_confirmation": "12345678"
}
```

**Resposta** `201`:

```json
{
  "data": {
    "id": 2,
    "name": "João Silva",
    "email": "joao@email.com",
    "email_verified_at": null,
    "created_at": "2026-02-24T12:00:00+00:00",
    "updated_at": "2026-02-24T12:00:00+00:00"
  }
}
```

**Validações:**
- `name`: obrigatório, string, máx. 100 caracteres
- `email`: obrigatório, formato e-mail, único na tabela `users`
- `password`: obrigatório, string, mín. 8 caracteres, confirmação obrigatória

#### Login

```
POST /api/auth/login
```

**Body:**

```json
{
  "email": "admin@salescommission.com",
  "password": "password"
}
```

**Resposta** `200`:

```json
{
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

#### Logout

```
POST /api/auth/logout
```

**Resposta** `200`:

```json
{ "message": "Successfully logged out" }
```

#### Perfil do usuário autenticado

```
GET /api/auth/me
```

**Resposta** `200`:

```json
{
  "data": {
    "id": 1,
    "name": "Admin",
    "email": "admin@salescommission.com",
    "email_verified_at": "2026-02-24T12:00:00+00:00",
    "created_at": "2026-02-24T12:00:00+00:00",
    "updated_at": "2026-02-24T12:00:00+00:00"
  }
}
```

---

### Vendedores

#### Criar vendedor

```
POST /api/sellers
```

**Body:**

```json
{
  "name": "Maria Souza",
  "email": "maria@email.com"
}
```

**Resposta** `201`:

```json
{
  "data": {
    "id": 11,
    "name": "Maria Souza",
    "email": "maria@email.com",
    "created_at": "2026-02-24T12:00:00+00:00",
    "updated_at": "2026-02-24T12:00:00+00:00"
  }
}
```

**Validações:**
- `name`: obrigatório, string, máx. 100 caracteres
- `email`: obrigatório, formato e-mail, único na tabela `sellers`

#### Listar vendedores

```
GET /api/sellers
GET /api/sellers?page=2&per_page=10
```

| Parâmetro  | Tipo | Padrão | Descrição                  |
|------------|------|--------|----------------------------|
| `page`     | int  | 1      | Página atual               |
| `per_page` | int  | 15     | Itens por página (máx. 100)|

**Resposta** `200`:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Carlos Lima",
      "email": "carlos@email.com",
      "created_at": "2026-02-24T12:00:00+00:00",
      "updated_at": "2026-02-24T12:00:00+00:00"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 10 }
}
```

#### Buscar vendedor

```
GET /api/sellers/{id}
```

**Resposta** `200`:

```json
{
  "data": {
    "id": 1,
    "name": "Carlos Lima",
    "email": "carlos@email.com",
    "created_at": "2026-02-24T12:00:00+00:00",
    "updated_at": "2026-02-24T12:00:00+00:00"
  }
}
```

#### Reenviar e-mail de comissão

```
POST /api/sellers/{id}/resend-commission
```

**Body (opcional):**

```json
{
  "date": "2026-02-24"
}
```

Se `date` não for enviado, usa a data atual.

**Resposta** `200`:

```json
{ "message": "Commission email queued successfully." }
```

---

### Vendas

#### Criar venda

```
POST /api/sales
```

**Body:**

```json
{
  "seller_id": 1,
  "value": 1500.00,
  "sale_date": "2026-02-24",
  "commission_rate": 8.50
}
```

O campo `commission_rate` é opcional (padrão **8,5%**). A comissão (R$ 127,50 neste exemplo) é calculada automaticamente.

**Resposta** `201`:

```json
{
  "data": {
    "id": 56,
    "seller_id": 1,
    "value": 1500.00,
    "commission": 127.50,
    "commission_rate": 8.50,
    "sale_date": "2026-02-24",
    "created_at": "2026-02-24T12:00:00+00:00",
    "updated_at": "2026-02-24T12:00:00+00:00",
    "seller": {
      "id": 1,
      "name": "Carlos Lima",
      "email": "carlos@email.com",
      "created_at": "2026-02-24T12:00:00+00:00",
      "updated_at": "2026-02-24T12:00:00+00:00"
    }
  }
}
```

**Validações:**
- `seller_id`: obrigatório, inteiro, deve existir na tabela `sellers`
- `value`: obrigatório, numérico, mínimo 0.01
- `sale_date`: obrigatório, formato de data válido
- `commission_rate`: opcional, numérico, entre 0 e 100 (padrão 8.50)

#### Listar vendas

```
GET /api/sales
GET /api/sales?seller_id=1&page=1&per_page=20
```

| Parâmetro   | Tipo | Padrão | Descrição                         |
|-------------|------|--------|-----------------------------------|
| `seller_id` | int  | —      | Filtra vendas por vendedor        |
| `page`      | int  | 1      | Página atual                      |
| `per_page`  | int  | 15     | Itens por página (máx. 100)       |

Resultados ordenados por `sale_date` (mais recente primeiro).

**Resposta** `200`:

```json
{
  "data": [
    {
      "id": 56,
      "seller_id": 1,
      "value": 1500.00,
      "commission": 127.50,
      "commission_rate": 8.50,
      "sale_date": "2026-02-24",
      "created_at": "2026-02-24T12:00:00+00:00",
      "updated_at": "2026-02-24T12:00:00+00:00",
      "seller": { "id": 1, "name": "Carlos Lima", "email": "carlos@email.com", "..." : "..." }
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 }
}
```

#### Buscar venda

```
GET /api/sales/{id}
```

**Resposta** `200`:

```json
{
  "data": {
    "id": 56,
    "seller_id": 1,
    "value": 1500.00,
    "commission": 127.50,
    "commission_rate": 8.50,
    "sale_date": "2026-02-24",
    "created_at": "2026-02-24T12:00:00+00:00",
    "updated_at": "2026-02-24T12:00:00+00:00",
    "seller": {
      "id": 1,
      "name": "Carlos Lima",
      "email": "carlos@email.com",
      "created_at": "2026-02-24T12:00:00+00:00",
      "updated_at": "2026-02-24T12:00:00+00:00"
    }
  }
}
```

---

### Relatórios

#### Resumo geral de vendas

```
GET /api/reports/sales
GET /api/reports/sales?start_date=2026-01-01&end_date=2026-02-28
```

| Parâmetro    | Tipo   | Padrão | Descrição          |
|--------------|--------|--------|--------------------|
| `start_date` | date   | —      | Início do período  |
| `end_date`   | date   | —      | Fim do período     |

**Resposta** `200`:

```json
{
  "data": {
    "total_sales": 55,
    "total_value": 45230.00,
    "total_commission": 3844.55,
    "average_value": 822.36,
    "average_commission": 69.90
  }
}
```

#### Vendas por vendedor

```
GET /api/reports/sales/by-seller
GET /api/reports/sales/by-seller?start_date=2026-01-01&seller_id=1
```

| Parâmetro    | Tipo   | Padrão | Descrição                   |
|--------------|--------|--------|-----------------------------|
| `start_date` | date   | —      | Início do período           |
| `end_date`   | date   | —      | Fim do período              |
| `seller_id`  | int    | —      | Filtra por vendedor         |

**Resposta** `200` (top 50, ordenado por valor total desc):

```json
{
  "data": [
    {
      "seller_id": 1,
      "seller_name": "Carlos Lima",
      "seller_email": "carlos@email.com",
      "total_sales": 8,
      "total_value": 12500.00,
      "total_commission": 1062.50
    }
  ]
}
```

#### Vendas diárias

```
GET /api/reports/sales/daily
GET /api/reports/sales/daily?start_date=2026-02-01&end_date=2026-02-28
```

| Parâmetro    | Tipo   | Padrão | Descrição                              |
|--------------|--------|--------|----------------------------------------|
| `start_date` | date   | —      | Início do período (padrão: -30 dias)   |
| `end_date`   | date   | —      | Fim do período (padrão: hoje)          |
| `seller_id`  | int    | —      | Filtra por vendedor                    |

Sem filtros, retorna os últimos 30 dias.

**Resposta** `200` (ordenado por data asc):

```json
{
  "data": [
    {
      "date": "2026-02-20",
      "total_sales": 5,
      "total_value": 3200.00,
      "total_commission": 272.00
    }
  ]
}
```

---

## Comissão

A comissão é calculada automaticamente no momento do cadastro da venda. A taxa é configurável por venda (padrão **8,5%**):

```
comissão = valor_da_venda × (commission_rate / 100)
```

| Campo             | Tipo   | Padrão | Descrição                        |
|-------------------|--------|--------|----------------------------------|
| `commission_rate` | float  | 8.50   | Taxa de comissão em % (0 a 100)  |
| `commission`      | float  | —      | Valor calculado automaticamente  |

O valor é arredondado para 2 casas decimais e armazenado junto à venda.

## E-mails

O sistema envia dois tipos de e-mail diário, agendados para **23:59** via scheduler:

| E-mail                          | Destinatário      | Conteúdo                                                     |
|---------------------------------|-------------------|--------------------------------------------------------------|
| Resumo de Vendas                | Cada vendedor     | Quantidade de vendas, valor total e comissão total do dia     |
| Resumo Administrativo de Vendas | Admin (`ADMIN_EMAIL`) | Vendedores ativos, quantidade de vendas e valor total do dia |

- Os e-mails são processados via **fila Redis** (assíncrono)
- Em desenvolvimento, podem ser visualizados no **Mailpit**: http://localhost:8025
- O reenvio manual pode ser feito via `POST /api/sellers/{id}/resend-commission`

## Variáveis de Ambiente

| Variável       | Descrição                                | Valor Padrão                  |
|----------------|------------------------------------------|-------------------------------|
| `DB_HOST`      | Host do MySQL                            | `mysql`                       |
| `DB_DATABASE`  | Nome do banco                            | `sales_commission`            |
| `DB_USERNAME`  | Usuário do banco                         | `sales_user`                  |
| `DB_PASSWORD`  | Senha do banco                           | `secret`                      |
| `REDIS_HOST`   | Host do Redis                            | `redis`                       |
| `QUEUE_CONNECTION` | Driver da fila                       | `redis`                       |
| `CACHE_STORE`  | Driver de cache                          | `redis`                       |
| `MAIL_HOST`    | Host SMTP                                | `mailpit`                     |
| `MAIL_PORT`    | Porta SMTP                               | `1025`                        |
| `ADMIN_EMAIL`  | E-mail que recebe o resumo administrativo| `admin@salescommission.com`   |
| `JWT_SECRET`   | Chave secreta do JWT (gerada pelo setup) | —                             |
| `JWT_TTL`      | Tempo de expiração do token (minutos)    | `60`                          |

## Testes

```bash
# Todos os testes
docker compose exec app php artisan test

# Por módulo
docker compose exec app php artisan test --group=auth
docker compose exec app php artisan test --group=seller
docker compose exec app php artisan test --group=sale
docker compose exec app php artisan test app/Api/Modules/Report
```

## Qualidade de Código

```bash
# Formatação (Laravel Pint — PSR-12 strict)
docker compose exec app ./vendor/bin/pint

# Análise estática (PHPStan nível 5 + Larastan)
docker compose exec app ./vendor/bin/phpstan analyse

# IDE helper (gera autocompletion para models e facades)
docker compose exec app php artisan ide-helper:generate
docker compose exec app php artisan ide-helper:models --write-mixin --no-interaction
```

## Arquitetura

O projeto segue uma **arquitetura modular** onde cada domínio (Auth, Seller, Sale) é isolado em seu próprio módulo dentro de `app/Api/Modules/`. Cada módulo possui suas camadas:

```
app/Api/Modules/
├── Auth/
│   ├── Controllers/        → Recebe a requisição HTTP e delega ao UseCase
│   ├── Data/               → DTOs com validação (Spatie Laravel Data)
│   ├── UseCases/           → Regra de negócio (uma ação por classe — SRP)
│   ├── Resources/          → Formatação da resposta JSON
│   └── Tests/              → Testes unitários e de integração
├── Seller/
│   ├── Controllers/
│   ├── Data/
│   ├── UseCases/
│   ├── Repositories/       → Acesso a dados (abstração do Eloquent)
│   ├── Resources/
│   └── Tests/
├── Sale/
│   ├── Controllers/
│   ├── Data/
│   ├── UseCases/
│   ├── Repositories/
│   ├── Resources/
│   ├── Jobs/               → Jobs assíncronos para envio de e-mails
│   ├── Mail/               → Mailables com templates HTML
│   └── Tests/
└── Report/
    ├── Controllers/        → Endpoints de relatórios
    ├── Data/               → DTOs de filtros (período, vendedor)
    ├── UseCases/           → Resumo geral, por vendedor, vendas diárias
    ├── Repositories/       → Queries de agregação (SUM, COUNT, GROUP BY)
    ├── Resources/          → Formatação JSON dos relatórios
    └── Tests/
```

### Fluxo de uma requisição

```
Request → Controller → DTO (validação) → UseCase (regra de negócio) → Repository (dados) → Resource (resposta)
```

## Estrutura Docker

```
.docker/
├── php/Dockerfile          → PHP 8.5-FPM com extensões (pdo_mysql, redis, etc.)
├── nginx/
│   ├── Dockerfile          → Nginx Alpine
│   └── default.conf        → Virtual host apontando para public/
└── mysql/
    └── my.cnf              → Configuração customizada do MySQL
```

| Container            | Descrição                                          |
|----------------------|----------------------------------------------------|
| `sales-api-app`      | PHP-FPM — executa a aplicação Laravel              |
| `sales-api-nginx`    | Nginx — proxy reverso para o PHP-FPM               |
| `sales-api-mysql`    | MySQL 9.x — banco de dados com health check        |
| `sales-api-redis`    | Redis Alpine — cache e fila                        |
| `sales-api-queue`    | Worker de fila — processa jobs (e-mails)           |
| `sales-api-scheduler`| Scheduler — executa tarefas agendadas (23:59)      |
| `sales-api-mailpit`  | Mailpit — captura e-mails em desenvolvimento       |
