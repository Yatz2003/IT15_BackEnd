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

Public endpoints:

- GET /api/weather
- GET /api/dashboard/weather

Protected with `auth:sanctum`:

- GET /api/dashboard/overview
- GET /api/dashboard/enrollment-analytics
- GET /api/dashboard/enrollment-trends
- GET /api/dashboard/attendance-patterns
- GET /api/dashboard/reliability-snapshot
- GET /api/dashboard/room-assignments
- GET /api/dashboard/room-availability
- GET /api/rooms
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
- `GET /api/weather`: `city` or (`lat` and `lon`)

## Weather Nexus

`GET /api/weather` returns current weather plus a full 7-day forecast with daily hourly points.

Example request:

```http
GET /api/weather?city=Manila
```

Example response:

```json
{
  "location": "Manila, PH",
  "current": {
    "temperature": 31,
    "humidity": 70,
    "wind_speed": 4,
    "description": "Partly Cloudy",
    "icon": "03d"
  },
  "forecast": [
    {
      "day": "Sat",
      "date": "2026-03-14",
      "temperature": 31,
      "icon": "03d",
      "hourly": [
        {"time": "09:00", "temp": 28},
        {"time": "12:00", "temp": 31},
        {"time": "15:00", "temp": 32},
        {"time": "18:00", "temp": 29}
      ]
    }
  ]
}
```

Common weather errors:

- `404` location not found
- `502` provider failure or invalid key
- `504` request timeout

## Enrollment Analytics (Yearly)

`GET /api/dashboard/enrollment-analytics` returns yearly enrollment growth percentages from 2015 up to the current year, derived from real student records.

Example response:

```json
[
  {"year": 2015, "percentage": 0},
  {"year": 2016, "percentage": 18.5},
  {"year": 2017, "percentage": 0}
]
```

## Attendance Patterns

`GET /api/dashboard/attendance-patterns` returns chart-ready attendance points by school day.

Example response:

```json
[
  {"date": "2026-01-10", "day": "2026-01-10", "attendance_rate": 92.0},
  {"date": "2026-01-11", "day": "2026-01-11", "attendance_rate": 88.0}
]
```

## Room Assignment and Availability

`GET /api/dashboard/room-assignments` includes subject and room fields for frontend tables/charts.

Example response:

```json
[
  {
    "subject": "Database Systems",
    "room": "Lab 2",
    "capacity": "30/45",
    "student_capacity": "30/45",
    "subject_name": "Database Systems",
    "room_number": "Lab 2",
    "schedule": "MWF 9:00-10:30",
    "availability": "Available"
  }
]
```

`GET /api/rooms` returns compact room assignment data:

```json
[
  {
    "subject": "Physics 1",
    "room": "Room 204",
    "capacity": "30/45",
    "student_capacity": "30/45",
    "schedule": "Mon/Wed 10:00-11:30"
  }
]
```

## Data Reliability Snapshot

`GET /api/dashboard/reliability-snapshot` returns concise system-wide metrics:

```json
{
  "total_students": 500,
  "total_programs": 20,
  "total_subjects": 100,
  "active_programs": 18,
  "average_attendance": 91
}
```

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