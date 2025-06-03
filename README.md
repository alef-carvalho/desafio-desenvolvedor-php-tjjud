# TJJUD - Desafio Técnico Desenvolvedor PHP
Sistema para gerenciamento de livraria.

## 1 - Pré-Requisitos
- Docker

## 2 - Instalação

### 2.1 - Clone o repositório

```shell
git clone https://github.com/alef-carvalho/desafio-desenvolvedor-php-tjjud.git
```

### 2.2 - Faça uma cópia do arquivo de configuração:

```shell
cd desafio-desenvolvedor-php-tjjud/  && cp .env.example .env
```
### 2.3 - Execute a aplicação:
```shell
docker compose up -d
```
A aplicação estará disponível no endereço: http://localhost/admin 

```
user: csilva.alef@gmail.com
pass: 123456
```

## 3 - Configuração
Gere uma chave para a aplicação:
```shell
docker compose exec app php artisan key:generate
```

Execute a migração da base de dados:
```shell
docker compose exec app php artisan migrate
```

Execute os seeders na base de dados
```shell
docker compose exec app php artisan db:seed
```

## Executando os Testes
O projeto conta com alguns testes de exemplo, o comando abaixo executa a suite e exibe o relatório de cobertura:
```shell
docker compose exec app php artisan test --coverage
```
## 5 - Documentação
A documentação da API (swagger) pode ser encontrada na URL:

**[http://localhost/api/docs](http://localhost/api/docs)**

## Tecnologias Utilizadas
- **PHP** (8.3)
- **Composer** (2.5.0)
- **Laravel** (12.0)
- **FilamentPHP** (3.3)
