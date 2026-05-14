# Domain Monitor

Панель администратора для автоматического мониторинга доступности доменов.

## Стек

- **Backend:** Laravel 13 (PHP 8.3)
- **База данных:** PostgreSQL 14
- **Frontend:** Blade + Tailwind CSS (Laravel Breeze)
- **Очередь:** Database queue driver
- **Инфраструктура:** Docker (Nginx + PHP-FPM + Queue Worker + Scheduler)

## Функционал

- Регистрация / вход / выход пользователей
- Добавление, редактирование и удаление доменов
- Настройки проверок: интервал (1–1440 мин), таймаут (1–60 сек), метод (GET / HEAD)
- Автоматические проверки доменов по расписанию (каждую минуту планировщик ставит просроченные домены в очередь)
- Ручная проверка «Check now» из интерфейса
- История проверок: дата, статус, HTTP-код, время ответа (ms), ошибка
- Dashboard со сводной статистикой (всего / UP / DOWN / не проверялись)
- Защита от SSRF — нельзя мониторить localhost и приватные IP-адреса
- Уникальность URL в рамках одного пользователя

## Быстрый старт (Docker)

### 1. Клонировать репозиторий

```bash
git clone <repo-url> domain-monitor
cd domain-monitor
```

### 2. Создать `.env`

```bash
cp .env.example .env
```

Убедитесь, что переменные базы данных совпадают с `docker-compose.yml` (по умолчанию настроено):

```dotenv
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=domain_monitor
DB_USERNAME=domain_monitor
DB_PASSWORD=domain_monitor_pass
```

### 3. Запустить

```bash
docker compose up -d
```

Сервис `init` автоматически выполнит:
- `composer install`
- `npm install && npm run build`
- `php artisan key:generate`
- `php artisan migrate`
- Кеширование конфигурации и роутов

Приложение будет доступно по адресу: **http://localhost:8099**

### 4. Остановить

```bash
docker compose down
```

## Сервисы Docker

| Сервис      | Описание                                     |
|-------------|----------------------------------------------|
| `postgres`  | PostgreSQL 14                                |
| `init`      | Одноразовая инициализация (миграции, сборка) |
| `app`       | PHP-FPM (Laravel)                            |
| `nginx`     | Веб-сервер Nginx                             |
| `queue`     | Воркер очереди (`queue:work`)                |
| `scheduler` | Планировщик (`schedule:work`)                |

## Локальная разработка (без Docker)

### Требования

- PHP 8.3+
- Composer
- Node.js 20+
- PostgreSQL 14+ (или другая СУБД)

### Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

npm install
npm run dev
```

### Запуск

```bash
composer dev
```

Эта команда запускает параллельно: сервер, queue worker, логи (Pail) и Vite.

## Архитектура проверок

```
schedule:work (каждую минуту)
  └─ domains:schedule-checks
       └─ CheckDomainJob (queue: database)
            └─ DomainCheckService
                 └─ DomainChecker (HTTP GET/HEAD)
                      └─ DomainCheck (history record)
```

- **`ShouldBeUnique`** на `CheckDomainJob` исключает дублирование задач одного домена в очереди
- **`lazy()`** в планировщике обрабатывает домены чанками, не загружая все в память

## Уведомления (не реализованы — bonus feature)

Заглушки подключения уведомлений находятся в `DomainCheckService`:

```php
// $domain->user->notify(new \App\Notifications\DomainDownNotification($domain, $domainCheck));
// $domain->user->notify(new \App\Notifications\DomainRecoveredNotification($domain, $domainCheck));
```

Для активации:
1. Создать Notification-классы (`php artisan make:notification DomainDownNotification`)
2. Настроить `MAIL_*` переменные в `.env`
3. Раскомментировать вызовы в `DomainCheckService::check()`

## Тесты

```bash
composer test
```
