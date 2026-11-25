# Vortex Vagas

Aplicação web para gerenciamento de vagas de emprego e candidaturas, desenvolvida como teste técnico para a vaga de Desenvolvedor Back-End.

Permite cadastrar vagas (CLT, Pessoa Jurídica ou Freelancer), cadastrar candidatos e inscrever candidatos em uma ou mais vagas. Vagas podem ser pausadas, o que impede novas candidaturas.

## Tecnologias

- PHP 8.4 / Laravel 13
- PostgreSQL 17
- Blade + Bootstrap 5 (HTML, CSS e JavaScript)
- PHPUnit
- Docker (nginx + php-fpm + postgres)

## Como executar

Pré-requisitos: [Docker](https://docs.docker.com/get-docker/) com Docker Compose e Git. Não é necessário ter PHP, Composer ou PostgreSQL instalados na máquina.

```bash
git clone https://github.com/Mateus-Moreira5/vortex-vagas.git
cd vortex-vagas

cp .env.example .env

docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

> No Windows (PowerShell), use `Copy-Item .env.example .env` no lugar do `cp`.

A aplicação fica disponível em **http://localhost:8000**.

O seed cria 50 vagas (40 abertas e 10 pausadas) e 80 candidatos com candidaturas aleatórias.

### Serviços

| Serviço | Container | Acesso |
|---|---|---|
| Aplicação (nginx) | `vortex-nginx` | http://localhost:8000 |
| PHP-FPM | `vortex-app` | interno |
| PostgreSQL | `vortex-db` | `localhost:5433` · banco `vortex` · usuário `vortex` · senha `secret` |

## Testes

```bash
docker compose exec app php artisan test
```

Os testes rodam em um banco PostgreSQL separado (`vortex_test`, criado automaticamente na primeira subida do container), sem afetar os dados de desenvolvimento.

O enunciado pede "testes unitários e de unidade". Interpretei como duas categorias:

- **Unitários** (`tests/Unit`): lógica isolada, sem HTTP e sem banco (enum de tipo de vaga, regras do model de vaga).
- **Feature** (`tests/Feature`): fluxo completo de requisição (rota, validação, controller, banco e resposta) dos CRUDs, filtros, paginação e candidaturas, além dos testes da regra de negócio de candidatura no `ApplicationService`.

## Funcionalidades

**Vagas**
- CRUD completo, com tipo CLT, Pessoa Jurídica ou Freelancer
- Pausar e reativar: vaga pausada não recebe novas candidaturas (as existentes são mantidas)
- Filtros por título, descrição, localização, tipo, status e faixa salarial

**Candidatos**
- CRUD completo, com e-mail único
- Filtros por nome, e-mail, telefone e faixa de data de nascimento

**Candidaturas**
- Um candidato pode se inscrever em uma ou mais vagas, a partir da página do candidato
- Bloqueio de inscrição em vaga pausada e de inscrição duplicada
- Cancelamento da candidatura pela página do candidato ou da vaga

**Em todas as listagens**
- Filtro por qualquer campo, combinável
- Paginação com 20 itens por página, ajustável para 10, 50 ou 100
- Exclusão a partir da listagem, retornando à mesma página e filtros
- Exclusão em massa: seleção por checkbox, com opção de selecionar todos da página
- Validação de campos obrigatórios e tipos de dados, com mensagens em português

## Arquitetura e decisões

```
app/
├── Enums/VacancyType.php            Tipos de vaga e seus rótulos
├── Exceptions/ApplicationException  Erros de regra de negócio da candidatura
├── Filters/                         Filtros de listagem (QueryFilter + um filtro por recurso)
├── Http/
│   ├── Controllers/                 Controllers finos, apenas coordenam
│   │   └── Concerns/                Traits compartilhados (paginação, redirect pós-exclusão)
│   └── Requests/                    Validação (Form Requests)
├── Models/                          Models Eloquent
│   └── Concerns/Filterable.php      Scope de filtro reutilizável
└── Services/ApplicationService.php  Regras da candidatura
```

- **Filtros com Template Method:** `QueryFilter` define o algoritmo (percorrer os filtros permitidos e aplicar os que vieram preenchidos) e cada recurso só declara seus filtros (`VacancyFilter`, `CandidateFilter`). A lista de filtros é fechada, então parâmetros desconhecidos são ignorados, assim como valores inválidos.
- **Validação em Form Requests:** os controllers recebem apenas dados válidos.
- **Regra de negócio em Service:** a candidatura é a única operação com regra de negócio (vaga pausada e duplicidade). Ela fica no `ApplicationService`, que pode ser reaproveitado por outras interfaces e testado de forma isolada. Os CRUDs simples usam o Eloquent diretamente, sem camadas de repositório, que só repassariam chamadas.
- **Integridade no banco:** índice único em `applications (candidate_id, vacancy_id)` e exclusão em cascata das candidaturas ao remover vaga ou candidato.
- **Enum** para o tipo de vaga, com cast automático no model.

## Rotas principais

| Método | URI | Descrição |
|---|---|---|
| GET | `/vacancies` | Listagem de vagas com filtros |
| GET/POST | `/vacancies/create`, `/vacancies` | Criação de vaga |
| GET/PUT | `/vacancies/{id}/edit`, `/vacancies/{id}` | Edição de vaga |
| DELETE | `/vacancies/{id}` | Exclusão de vaga |
| DELETE | `/vacancies` | Exclusão em massa de vagas |
| PATCH | `/vacancies/{id}/toggle-pause` | Pausar ou reativar vaga |
| GET | `/candidates` | Listagem de candidatos com filtros |
| GET/POST | `/candidates/create`, `/candidates` | Criação de candidato |
| GET/PUT | `/candidates/{id}/edit`, `/candidates/{id}` | Edição de candidato |
| DELETE | `/candidates/{id}` | Exclusão de candidato |
| DELETE | `/candidates` | Exclusão em massa de candidatos |
| POST | `/candidates/{id}/applications` | Inscrever candidato em vaga |
| DELETE | `/candidates/{id}/applications/{vacancyId}` | Cancelar candidatura |

## Bônus

- [x] Alterar a quantidade de itens por página (10, 20, 50 ou 100)
- [x] Exclusão em massa (`public/js/bulk-delete.js` controla a seleção)
- [ ] Autenticação de usuário
- [ ] API REST JSON

## Comandos úteis

```bash
docker compose exec app php artisan migrate:fresh --seed   # recria o banco com dados de exemplo
docker compose exec app php artisan route:list             # lista as rotas
docker compose exec app ./vendor/bin/pint                  # formata o código
docker compose logs -f app                                 # logs do PHP
docker compose down                                        # para os containers
docker compose down -v                                     # para os containers e apaga o banco
```
