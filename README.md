# Laravel ZIP API Client - Kollár András 13.P

Laravel frontend application for consuming the ZIP API.

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## API Endpoints

| Endpoint | Auth? | Description |
| -------- | ----- | ----------- |
| GET `/counties` | ❌ | List all counties |
| GET `/counties/{id}` | ❌ | Get single county |
| GET `/counties?needle={search}` | ❌ | Search counties |
| POST `/counties` | ✅ | Create county |
| PUT `/counties/{id}` | ✅ | Update county |
| DELETE `/counties/{id}` | ✅ | Delete county |
| GET `/cities` | ❌ | List all cities |
| GET `/cities/{id}` | ❌ | Get single city |
| GET `/cities?county_id={id}` | ❌ | Get cities by county |
| GET `/cities?county_id={id}&letter={letter}` | ❌ | Get cities by county and first letter |
| GET `/cities/letters?county_id={id}` | ❌ | Get available first letters for county |
| POST `/cities` | ✅ | Create city |
| PUT `/cities/{id}` | ✅ | Update city |
| DELETE `/cities/{id}` | ✅ | Delete city |
