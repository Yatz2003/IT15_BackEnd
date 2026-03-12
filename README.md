# School Dashboard API (Laravel)

Backend API for a full-stack school dashboard application.

## Stack

- Laravel 12
- Laravel Sanctum authentication
- MySQL or PostgreSQL
- JSON REST API for React frontend

## Setup

1. Install dependencies:

```bash
composer install
```

2. Create environment file and app key:

```bash
copy .env.example .env
php artisan key:generate
```

3. Configure `.env`:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_dashboard
DB_USERNAME=root
DB_PASSWORD=

# React frontend origin
FRONTEND_URL=http://localhost:5173

# Optional HTTPS forcing behind reverse proxy
FORCE_HTTPS=false
```

4. Run migrations and seed data:

```bash
php artisan migrate:fresh --seed
```

5. Start the server:

```bash
php artisan serve
```

## Seeded Data

- Courses: 22 records
- Students: 500 records minimum
- School days: weekday academic calendar with holiday/attendance data

## Authentication (Sanctum)

### `POST /api/login`

Request body:

```json
{
	"email": "admin@example.com",
	"password": "password",
	"device_name": "react-web"
}
```

Response (`200`):

```json
{
	"user": {
		"id": 1,
		"name": "Test User",
		"email": "admin@example.com"
	},
	"token": "1|plain-text-token",
	"token_type": "Bearer"
}
```

### `POST /api/logout` (auth required)

Response (`200`):

```json
{
	"message": "Logged out successfully."
}
```

### `GET /api/user` (auth required)

Response (`200`):

```json
{
	"data": {
		"id": 1,
		"name": "Test User",
		"email": "admin@example.com"
	}
}
```

## API Endpoints

### Students

- `GET /api/students`
- `GET /api/students/enrollment-trends`

Example `GET /api/students/enrollment-trends`:

```json
{
	"data": [
		{ "period": "2025-10", "total": 29 },
		{ "period": "2025-11", "total": 34 }
	]
}
```

### Courses

- `GET /api/courses`
- `GET /api/courses/distribution`

Example `GET /api/courses/distribution`:

```json
{
	"data": [
		{ "course_id": 1, "course_name": "BS Computer Science", "student_count": 28 },
		{ "course_id": 2, "course_name": "BS Information Technology", "student_count": 25 }
	]
}
```

### School Days

- `GET /api/school-days`
- `GET /api/attendance`

Example `GET /api/attendance`:

```json
{
	"data": [
		{ "date": "2026-01-05", "attendance_rate": 93.4 },
		{ "date": "2026-01-06", "attendance_rate": 91.2 }
	],
	"summary": {
		"average_attendance_rate": 89.75,
		"days_counted": 210
	}
}
```

### Dashboard

- `GET /api/dashboard/summary` (auth required)

Example response:

```json
{
	"data": {
		"students_total": 500,
		"courses_total": 22,
		"school_days_total": 230,
		"average_attendance_rate": 88.94,
		"latest_school_day": {
			"date": "2026-06-30",
			"attendance_rate": 92.2,
			"is_holiday": false
		}
	}
}
```

## Security Notes

- Input validation via Form Requests (login) and strict model casting.
- CORS configured in `config/cors.php`.
- Secrets must remain in `.env` and never be committed.
- API is HTTPS-ready with optional forced HTTPS (`FORCE_HTTPS=true`).