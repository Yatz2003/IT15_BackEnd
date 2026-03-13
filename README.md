# School Dashboard API (Laravel)

Backend API for a React school academic dashboard.

## Stack

- Laravel 12
- MySQL
- Laravel Sanctum (token auth)
- JSON REST API

## Quick Setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Set your frontend origin in `.env`:

```env
FRONTEND_URL=http://localhost:5173
```

## Seeded Data

- Programs: 22+ records
- Students: 500+ records
- Subjects: seeded and linked to programs
- School days: academic dates with attendance and holiday flags

## Authentication (Sanctum)

All endpoints below require `Authorization: Bearer <token>` except `/api/login`.

### `POST /api/login`

Request:

```json
{
	"email": "admin@example.com",
	"password": "passwordjames",
	"device_name": "react-web"
}
```

Response `200`:

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

### `POST /api/logout`

Response `200`:

```json
{
	"message": "Logged out successfully."
}
```

### `GET /api/user`

Response `200`:

```json
{
	"user": {
		"id": 1,
		"name": "Test User",
		"email": "admin@example.com"
	}
}
```

## Dashboard API Endpoints

- `GET /api/dashboard/overview`
- `GET /api/students`
- `GET /api/programs`
- `GET /api/subjects`
- `GET /api/enrollments`
- `GET /api/reports`

Optional query params for list endpoints:

- `per_page` (integer, min `1`, max `200`)

### Example: `GET /api/dashboard/overview`

```json
{
	"data": {
		"students_total": 500,
		"programs_total": 22,
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

### Example: `GET /api/programs`

```json
{
	"data": [
		{
			"id": 1,
			"program_name": "BS Computer Science",
			"department": "Computing",
			"enrolled_students": 31
		}
	],
	"meta": {
		"current_page": 1,
		"last_page": 1,
		"per_page": 100,
		"total": 22
	},
	"charts": {
		"students_by_department": [
			{ "department": "Computing", "students_total": 142 }
		]
	}
}
```

### Example: `GET /api/subjects`

```json
{
	"data": [
		{
			"id": 1,
			"code": "CS101",
			"subject_name": "Introduction to Computing",
			"program_id": 1,
			"program_name": "BS Computer Science",
			"department": "Computing",
			"units": 3
		}
	]
}
```

### Example: `GET /api/enrollments`

```json
{
	"data": [
		{
			"enrollment_id": 1,
			"program_id": 1,
			"student": {
				"id": 1,
				"name": "Jane Doe",
				"email": "jane@example.com"
			},
			"program": {
				"id": 1,
				"name": "BS Computer Science",
				"department": "Computing"
			},
			"status": "active",
			"enrolled_at": "2026-03-13T09:00:00+00:00"
		}
	]
}
```

### Example: `GET /api/reports`

```json
{
	"data": {
		"overview": {
			"students_total": 500,
			"programs_total": 22,
			"subjects_total": 12,
			"enrollments_total": 500
		},
		"attendance": {
			"average_rate": 89.75,
			"days_counted": 210
		},
		"students_per_program": [
			{
				"program_id": 1,
				"program_name": "BS Computer Science",
				"student_count": 31
			}
		]
	}
}
```

## Security

- Login uses request validation (`LoginRequest`).
- Listing endpoints validate input (`per_page`) before querying.
- Protected routes are under `auth:sanctum`.
- CORS is configured in `config/cors.php` with `FRONTEND_URL` support.