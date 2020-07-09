# Test task for Alter

Сервис реализован с помощью стека *PHP7/ApiPlatform/Symfony5/RoadRunner/PostgreSQL*. *ApiPlatform* - удобное и 
мощное решение для создания REST API и GraphQL сервисов, с поддержкой Swagger/OpenAPI "из коробки", основанное
на фреймворке *Symfony*. Сервер приложений *RoadRunner* позволяет интегрировать среду Go и PHP, управляя запуском
PHP-приложений внутри Go-рутин и балансируя нагрузки между ними; внутри каждого экземпляра приложения PHP 
(worker) запросы обрабатываются в цикле, что предотвращает необходимость инициализации PHP-приложения при 
каждом запросе. Это позволит нам обрабатывать запросы максимально быстро.

Реализован деплой и работа сервиса в кластере *Kubernetes* для максимально гибкого, автоматизированного и масштабируемого
управления работой приложения в продакшне, а также его мониторинга (в рамках тестового задания мониторинг не реализован,
но тот же *Prometeus* интегрируется легко с *RoadRunner* и *Kubernetes*).

Выложил сервис в Kubernetes-облако [DigitalOcean](https://www.digitalocean.com/products/kubernetes/), 
он доступен по адресу: http://alter-test.chuprunov.name

## Деплой в кластер Kubernetes

`export DATABASE_URL=postgresql://dbuser:dbpasswd@dbhost/alter_test; envsubst  <deploy/do-manifest.yaml | kubectl apply -f -`

(не прописываем секреты в конфигах. В настоящем продакшне их следует хранить в особом хранилище, например,
*Vault*. Для целей тестового задания подойдёт и передача через переменную окружения).

См. подробнее [deploy](./deploy)

## Запуск локально

Контейнер Docker:

`docker run --env DATABASE_URL=postgres://postgres:postgres@localhost/alter_test?server_version=12 --network="host" unitedt/alter-test-app`

Обязательно указать базу данных (в примере на локальном хосте). Swagger UI и API будут доступны на http://localhost:8080

## Инициализация базы

`bin/console doctrine:database:create`
`bin/console doctrine:schema:create`

Заполнение тестовыми фикстурами:

`bin/console hautelook:fixtures:load`

## Функциональное тестирование

Для функционального тестирования написаны тесты (исполняемые спецификации) на языке *Gherkin*. 

Для запуска тестов:

`vendor/bin/behat`

## Нагрузочное тестирование

Я применил утилиту siege для организации нагрузочного тестирования сервиса. Запустить тестирование можно командой:

`/loadtest/run_siege.sh http://alter-test.chuprunov.name`

Скрипт [./loadtest/run_siege.sh](./loadtest/run_siege.sh) настроен на тестирование в 4 потока, всего 10000 запросов.

