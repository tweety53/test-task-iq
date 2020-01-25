# Используемые инструменты и технологии

* `PHP 7.4`
* `Symfony 5`
* `MySQL 5.7`
* `RabbitMq`

# Развертывание приложения

1. Перейти в папку с проектом `cd test-task-iq/`
2. Выполнить `docker-compose -f "docker-compose.yml" up -d --build`
3. Зайти в php-fpm контейнер `docker exec -it php-fpm bash`

    Далее из контейнера:

1. Выполнить `composer install`
2. Выполнить
 ```
   ./bin/console doctrine:database:create
   ./bin/console doctrine:migrations:migrate
   ./bin/console doctrine:database:import database/initial_data.sql
   ```
3. Выполнить `./bin/phpunit ` либо `./bin/phpunit --coverage-html tests/output` если нужно посмотреть покрытие тестами
4. Если все ок в тестах - сервис готов к использованию

# Общий механизм работы

1. Запускается N воркеров ожидающих сообщения на обработку (из корня проекта: `./bin/console enqueue:consume --setup-broker -vvv` + что-то по типу ` >/dev/null 2>&1` приписать если необходимо, логи все равно идут в файл в `/var/log`) 
    
    PS ничего для контроля воркеров по типу supervisord / своих скриптов не прикручено (+ также после запуска в консоли может не работать ctrl + c , как я понял из-за особенностей amqp (забирает поток или чето такое :) )
    
2. Инициализируется сообщение с данными по необходимой операции и отправляется сервису по HTTP, POST запросом

    * Пополнение счета: `/account/deposit` с параметрами
       * user_to[integer] - ID аккаунта-получателя
       * amount[string] - сумма
    * Снятие со счета: `/account/withdrawal` с параметрами
       * user_from[integer] - ID аккаунта-получателя
       * amount[string] - сумма
    * Перевод средств: `/account/transfer` с параметрами
       * user_from[integer] - ID аккаунта-отправителя
       * user_to[integer] - ID аккаунта-получателя
       * amount[string] - сумма
       
2.1 Ответы сервера на запрос из п.2:

   * Успешный запрос - 200 OK:
   ```
{
    "result": "OK"
}
``` 
   * Запрос с какой-либо ошибкой - 400 Bad Request:
   ```
{
    "errors": [
        {
            "[<field_name>|fatal]": "<Error description>"
        },
        .......
    ]
}
```

Далее:

1. Отправляется сообщение с информацией по операции брокеру очередей
2. Сообщение получает первый свободный воркер и обрабатывает, для лока аккаунта взят pessimistic lock на уровне бд
3. В случае ошибок в запросах БД для обновления данных счета \ записи в историю операций - инфо с входными внешними данными + информацией об ошибке пишется в лог файл

PS вещи по типу обработки запросов на получение данных аккаунта\ истории операций от внешних сервисов не реализованы (надеюсь не нужно было :) )
PPS возможно требовалось инициировать publish сообщений не по http а с консоли, но подумал об этом только при дописывании readme
    
