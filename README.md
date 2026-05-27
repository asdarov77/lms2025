# LMS 2025 - Laravel Learning Management System

## Структура проекта

### Основные модели (Models)
- **Aircraft** - Классы воздушных судов (Ил-76, Ми-38 и т.д.)
- **Course** - Курсы/АУК (авиационные учебные курсы)
- **Category** - Категории вопросов/курсов
- **Aukstructure** - Иерархическая структура курсов (Тема → Раздел → Модуль)
- **Link** - Ссылки на файлы модулей
- **Question** - Вопросы для тестирования
- **Answer** - Варианты ответов
- **Group** - Учебные группы
- **Group2learning** - Запись групп на курсы с расписанием
- **User** - Пользователи системы
- **Role** - Роли пользователей
- **Permission** - Права доступа
- **Favorite** - Избранное пользователей
- **GradeBoundary** - Границы оценок

### Контроллеры (Controllers)
- **AuthController** - Аутентификация, регистрация, управление пользователями
- **CourseController** - CRUD операций с курсами
- **CategoryController** - Управление категориями
- **AircraftController** - Управление классами ВС
- **QuestionsController** - Управление вопросами тестов
- **GroupController** - Управление группами
- **Group2learningController** - Расписание занятий
- **PrivateController** - Потоковая передача файлов курсов
- **SearchController** - Поиск по содержимому курсов
- **FavoriteController** - Избранное
- **GradeBoundaryController** - Настройка оценок

### API Routes
Все API endpoints находятся в `routes/api.php`

#### Публичные маршруты:
- `POST /api/login` - Вход
- `POST /api/register` - Регистрация

#### Защищенные маршруты (требуют auth:sanctum):
- `/api/users` - Управление пользователями
- `/api/courses` - Курсы
- `/api/categories` - Категории
- `/api/aircrafts` - Классы ВС
- `/api/questions` - Вопросы
- `/api/groups` - Группы
- `/api/group2learnings` - Запись на курсы
- `/api/permissions` - Права доступа
- `/api/roles` - Роли
- `/api/private/{aircraft}/{auk}/{path?}` - Файлы курсов
- `/api/search` - Поиск
- `/api/favorites` - Избранное
- `/api/grade-boundaries` - Оценки

## Установка

1. Установите зависимости:
```bash
composer install
npm install
```

2. Скопируйте `.env.example` в `.env` и настройте базу данных

3. Создайте ключ приложения:
```bash
php artisan key:generate
```

4. Выполните миграции:
```bash
php artisan migrate
```

5. Запустите сервер разработки:
```bash
php artisan serve
npm run dev
```

## Конфигурация

В `.env` файле укажите:
```
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms2025
DB_USERNAME=root
DB_PASSWORD=secret
```

Для хранения файлов курсов создайте симлинк:
```bash
php artisan storage:link
```

Поместите файлы курсов в `storage/app/private/{aircraft}/{auk}/`

## Frontend

Проект использует Vue 3 + Vuetify 3 + Vite.
Фронтенд компоненты находятся в `resources/js/`.

## Лицензия

MIT
