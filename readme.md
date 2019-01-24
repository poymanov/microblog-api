# Microblog (API)

## Описание

API для приложения, повторяющего идеи Twitter.

API для работы в Single Page Application - https://github.com/poymanov/microblog-frontend

## Установка

- Расположить проект в необходимой директории
- В консоли перейти в директорию проекта
- Выполнить:
```
composer install
```

Для локального запуска проекта проще всего использовать [Laravel Homestead](https://github.com/poymanov/laravel-homestead-how-to).


## Настройка

В корне директории проекта создать файл `.env`:
```
cp .env.example .env
```

В нём необходимо задать параметры окружения приложения.

База данных:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=microblog_api
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Адрес, с которого к приложению разрешены CORS (по-умолчанию со всех адресов):
```
CORS_ALLOWED_ORIGIN=http://localhost:8080
```
Сформировать секретный ключ приложения. Выполнить в консоли:
```
php artisan key:generate
```
Применить миграции к базе данных:
```
php artisan migrate
```

## Тестирование

- В консоли перейти в директорию проекта
- Выполнить:
```
// В виртуальной машине через Laravel Homestead
phpunit

// При прочих вариантах установки
vendor/bin/phpunit
```

## Цели проекта

Код создан в учебных целях.
