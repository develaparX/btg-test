## Tech Stack

- **Frontend**: Laravel + Bootstrap 5
- **Backend**: Golang + Gorilla Mux (Clean Architecture)
- **Database**: PostgreSQL
- **Container**: Docker Compose

### Prerequisites

- PHP 8.0+, Composer
- Go 1.21+
- Docker & Docker Compose

### Setup

1. **Start Database**

```bash
docker compose up -d
```

2. **Run Laravel Frontend**

```bash
cd client-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

3. **Run Golang Backend**

```bash
cd server-golang
go mod tidy
go run cmd/main.go
```

### Access

- **Frontend**: http://localhost:8000
- **API**: http://localhost:8080
- **Database**: PostgreSQL:5432

## API Endpoints

```
GET    /api/v1/customers      # List customers
POST   /api/v1/customers      # Create customer
GET    /api/v1/customers/{id} # Get customer
PUT    /api/v1/customers/{id} # Update customer
DELETE /api/v1/customers/{id} # Delete customer
GET    /api/v1/nationalities  # Get nationalities
```

## Project Structure

```
├── client-laravel/    # Laravel frontend
├── server-golang/     # Golang API (Clean Architecture)
├── migrations/        # Database migrations
└── compose.yml       # Docker configuration
```
