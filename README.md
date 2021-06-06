# Sendmoney API
## _Api de que possibilita envio de dinheiro entre usuários_

## Tecnologias

API utilizadas no desenvolvimento:

- PHP
- Laravel
- REST
- Docker
- Mysql
- Tests

## Instalação

Requer: Docker

```sh
git clone https://github.com/MarcusRenato/sendmoney.git
cd sendmoney
cp .env.example
docker-compose up -d
docker-compose run --rm app composer install
docker-compose run --rm app php artisan key:generate
docker-compose run --rm app php artisan jwt:secret
docker-compose run --rm app php artisan migrate
```


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
