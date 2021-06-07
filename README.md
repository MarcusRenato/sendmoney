# Sendmoney API
## _Api de que possibilita envio de dinheiro entre usuários_

## Tecnologias

Api desenvolvida com as seguintes tecnologias e ferramentas:

- PHP
- Laravel
- REST
- Docker
- Mysql
- Tests

## Instalação

Requerimentos para iniciar aplicação: `Docker`

```sh
git clone https://github.com/MarcusRenato/sendmoney.git
cd sendmoney
cp .env.example .env
docker-compose up -d
docker-compose run --rm app composer install
docker-compose run --rm app php artisan key:generate
docker-compose run --rm app php artisan jwt:secret
docker-compose run --rm app php artisan migrate
```

A aplicação estará disponível no link: `http://localhost:8888`

### Documentação API
Para gerar a documentação da API insira o comando:
```
docker-compose run --rm app php artisan l5-swagger:generate
```
A Documentação pode ser acessada pelo link: `http://localhost:8888/api/documentation`

### Estilo, Análise e Testes
Caso prefira, para a executar o php-cs-fix, phpstan e os testes, no mesmo comando, utilize o seguinte comando:
```
docker-compose run --rm app composer php-check
```

### Observações
- Por padrão todo usuário criado via api inicia com o valor de $150 na carteira.

## Tests

Para executar os tests unitários e de api:
 ```
 docker-compose run --rm app php artisan test
 ```

## Queue

Até a seguinte publicação ainda não consegui deixar a fila iniciada automaticamente. Assim sendo, para que os jobs enfileirados seja executados é preciso executar o seguinte comando:
  ```
 docker-compose run --rm app php artisan queue:work
 ```
