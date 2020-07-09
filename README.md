# Test task for Alter


## Запуск

Контейнер Docker:

`docker run --env DATABASE_URL=postgres://postgres:postgres@localhost/alter_test?server_version=12 --network="host" unitedt/alter-test-app`

Обязательно указать базу данных (в примере на локальном хосте). Swagger UI и API будут доступны на http://localhost:8080