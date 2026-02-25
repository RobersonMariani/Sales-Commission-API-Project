# Sales Commission API

API RESTful para gerenciamento de vendas e cálculo de comissões de vendedores, construída com Laravel 12.

## Stack

- **PHP 8.4+** / **Laravel 12**
- **MySQL 9.x** — banco de dados
- **Redis** — cache e filas
- **Docker** — ambiente de desenvolvimento
- **JWT** — autenticação via `php-open-source-saver/jwt-auth`
- **Spatie Laravel Data** — DTOs com validação
- **Mailpit** — servidor SMTP local para testes de email

## Setup com Docker

```bash
# Clonar o repositório
git clone https://github.com/RobersonMariani/Sales-Commission-API-Project.git
cd Sales-Commission-API-Project

# Copiar o .env
cp .env.example .env

# Subir os containers
docker compose up -d

# Instalar dependências
docker compose exec app composer install

# Gerar chaves
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret

# Rodar migrations e seeders
docker compose exec app php artisan migrate --seed
```

## Acesso

| Serviço  | URL                    |
|----------|------------------------|
| API      | http://localhost:8000  |
| Mailpit  | http://localhost:8025  |
| MySQL    | localhost:3306         |
| Redis    | localhost:6379         |

## Dados do Seeder

- **Admin:** admin@salescommission.com / password
- **Vendedores:** 10 vendedores criados via factory
- **Vendas:** ~50 vendas distribuídas nos últimos 30 dias

## Endpoints

### Auth

| Método | Endpoint             | Descrição             | Auth |
|--------|----------------------|-----------------------|------|
| POST   | /api/auth/register   | Registrar usuário     | Não  |
| POST   | /api/auth/login      | Login (retorna JWT)   | Não  |
| POST   | /api/auth/logout     | Logout (invalida JWT) | Sim  |
| GET    | /api/auth/me         | Usuário autenticado   | Sim  |

### Sellers

| Método | Endpoint                              | Descrição               | Auth |
|--------|---------------------------------------|-------------------------|------|
| POST   | /api/sellers                          | Criar vendedor          | Sim  |
| GET    | /api/sellers                          | Listar vendedores       | Sim  |
| GET    | /api/sellers/{id}                     | Buscar vendedor         | Sim  |
| POST   | /api/sellers/{id}/resend-commission   | Reenviar email comissão | Sim  |

### Sales

| Método | Endpoint                 | Descrição             | Auth |
|--------|--------------------------|-----------------------|------|
| POST   | /api/sales               | Criar venda           | Sim  |
| GET    | /api/sales               | Listar vendas         | Sim  |
| GET    | /api/sales?seller_id=X   | Vendas por vendedor   | Sim  |
| GET    | /api/sales/{id}          | Buscar venda          | Sim  |

## Autenticação

Usar o header `Authorization: Bearer {token}` em todas as requisições autenticadas.

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@salescommission.com", "password": "password"}'

# Usar o token retornado
curl http://localhost:8000/api/sellers \
  -H "Authorization: Bearer {token}"
```

## Comissão

A comissão é calculada automaticamente em **8.5%** sobre o valor da venda no momento do cadastro.

## Emails

- **Email diário para vendedores** — resumo de vendas, valor total e comissão do dia
- **Email diário para admin** — soma de todas as vendas do dia
- **Reenvio manual** — `POST /api/sellers/{id}/resend-commission` (aceita parâmetro `date` opcional)
- Emails são processados via **fila Redis** e podem ser visualizados no **Mailpit** (http://localhost:8025)
- O scheduler dispara os emails diários às 23:59

## Testes

```bash
# Rodar todos os testes
docker compose exec app php artisan test

# Rodar testes de um módulo
docker compose exec app php artisan test --group=auth
docker compose exec app php artisan test --group=seller
docker compose exec app php artisan test --group=sale
```

## Qualidade de Código

```bash
# Formatar com Laravel Pint
docker compose exec app ./vendor/bin/pint

# Análise estática com PHPStan (nível 5)
docker compose exec app ./vendor/bin/phpstan analyse
```

## Arquitetura

```
app/Api/Modules/
├── Auth/
│   ├── Controllers/AuthController.php
│   ├── Data/LoginData.php, RegisterData.php
│   ├── UseCases/LoginUseCase.php, RegisterUseCase.php, LogoutUseCase.php, GetMeUseCase.php
│   ├── Resources/AuthResource.php, UserResource.php
│   └── Tests/
├── Seller/
│   ├── Controllers/SellerController.php
│   ├── Data/CreateSellerData.php, SellerQueryData.php
│   ├── UseCases/CreateSellerUseCase.php, GetSellerUseCase.php, GetSellersUseCase.php, ResendCommissionUseCase.php
│   ├── Repositories/SellerRepository.php
│   ├── Resources/SellerResource.php
│   └── Tests/
└── Sale/
    ├── Controllers/SaleController.php
    ├── Data/CreateSaleData.php, SaleQueryData.php
    ├── UseCases/CreateSaleUseCase.php, GetSaleUseCase.php, GetSalesUseCase.php
    ├── Repositories/SaleRepository.php
    ├── Resources/SaleResource.php
    ├── Jobs/SendDailySellerCommissionJob.php, SendDailyAdminSummaryJob.php
    ├── Mail/DailySellerCommissionMail.php, DailyAdminSummaryMail.php
    └── Tests/
```

Cada UseCase segue o **Single Responsibility Principle** — uma ação por classe.

## Estrutura Docker

```
.docker/
├── php/
│   └── Dockerfile          # PHP 8.4-FPM com extensões
├── nginx/
│   ├── Dockerfile          # Nginx Alpine
│   └── default.conf        # Config do virtual host
└── mysql/
    └── my.cnf              # Config customizada do MySQL
docker-compose.yml          # Orquestração dos serviços
```
