# University Enrollment Dashboard API

Laravel REST API backend for a React enrollment analytics dashboard.

## Stack

- Laravel 12
- MySQL
- Laravel Sanctum (Bearer token auth)
- REST JSON API

## Quick Setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Set React frontend origins in `.env`:

```env
FRONTEND_URLS=http://localhost:5173,http://localhost:3000
```

## Data Model

Core entities and relationships:

- Program: has many students, has many subjects
- Student: belongs to program, has many enrollments
- Subject: belongs to program, optional prerequisite subject, has many enrollments
- Enrollment: belongs to student, belongs to subject
- SchoolDay: attendance trend points with holiday flag

## Seeded Data

- Programs: 24 records (`program_name`, `department`, `is_active`)
- Students: 500 records (`name`, `email`, `program_id`, `year_level`)
- Subjects: generated per program with optional `prerequisite_id`
- Enrollments: realistic student-to-subject enrollment records
- SchoolDays: academic calendar with `attendance_rate` and holiday flags

## Authentication (Sanctum)

All dashboard/data endpoints require `Authorization: Bearer <token>`.

### POST /api/login

Request:

```json
{
  "email": "admin@example.com",
  "password": "passwordjames",
  "device_name": "react-web"
}
```

Response:

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

### POST /api/logout

Revokes the current token.

### GET /api/user

Returns the authenticated user.

## API Endpoints

Protected with `auth:sanctum`:

- GET /api/dashboard/overview
- GET /api/students
- GET /api/programs
- GET /api/subjects
- GET /api/enrollments
- GET /api/reports

### Query Parameters

- `GET /api/students`: `per_page`, `program_id`, `year_level`, `q`
- `GET /api/programs`: `per_page`, `department`, `is_active`
- `GET /api/subjects`: `per_page`, `program_id`, `q`
- `GET /api/enrollments`: `per_page`, `student_id`, `subject_id`, `program_id`
- `GET /api/dashboard/overview`: `months`
- `GET /api/reports`: `months`

## Dashboard Overview Response

`GET /api/dashboard/overview` returns chart-ready data:

```json
{
  "students_count": 500,
  "programs_count": 24,
  "active_programs_count": 22,
  "subjects_count": 230,
  "monthly_enrollment": [
    { "month": "2026-01", "total": 143 },
    { "month": "2026-02", "total": 167 }
  ],
  "course_distribution": [
    {
      "program_id": 1,
      "program_name": "BS Computer Science",
      "students_count": 28
    }
  ],
  "attendance_trends": [
    {
      "date": "2026-03-10",
      "attendance_rate": 91.2,
      "is_holiday": false
    }
  ]
}
```

## Security and Validation

- Input sanitization is enforced using dedicated FormRequest classes:
  - `LoginRequest`
  - `StudentsIndexRequest`
  - `ProgramsIndexRequest`
  - `SubjectsIndexRequest`
  - `EnrollmentsIndexRequest`
  - `DashboardOverviewRequest`
  - `ReportsIndexRequest`
- API data routes are protected by `auth:sanctum`.
- CORS is configured in `config/cors.php` using `FRONTEND_URLS` with React local defaults.