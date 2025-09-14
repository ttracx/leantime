# Installation Guide

## System Requirements

- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Web Server**: Apache or Nginx (IIS supported with modifications)
- **Memory**: Minimum 512MB RAM (1GB+ recommended)
- **Storage**: 100MB+ for application files

### Required PHP Extensions

- bcmath, ctype, curl, dom, exif, fileinfo, filter, gd, hash
- ldap, mbstring, mysqli, opcache, openssl, pcntl, pcre
- pdo, phar, session, tokenizer, zip, simplexml

## Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/Safe4Work/safe4work.git
cd safe4work
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm install && npm run production
```

### 3. Configure Environment

```bash
# Copy environment configuration
cp config/sample.env .env

# Edit the .env file with your settings
nano .env
```

### 4. Set Up Database

```bash
# Run database migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed
```

### 5. Set Permissions

```bash
# Set proper permissions for Laravel
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Access the Application

Navigate to your web server URL and complete the setup wizard.

## Development Setup

For development, use the following commands:

```bash
# Install all dependencies including dev tools
composer install
npm install

# Run database migrations and seeders
php artisan migrate:fresh --seed

# Start development server
php artisan serve

# Watch for asset changes
npm run watch
```

## Docker Installation

### Using Docker Compose (Recommended)

```bash
# Clone the repository
git clone https://github.com/Safe4Work/safe4work.git
cd safe4work

# Start with Docker Compose
docker-compose up -d
```

### Using Docker Directly

```bash
docker run -d --name safe4work \
  -p 8080:80 \
  -e LEAN_DB_HOST=mysql_host \
  -e LEAN_DB_USER=admin \
  -e LEAN_DB_PASSWORD=secret \
  -e LEAN_DB_DATABASE=safe4work \
  safe4work/safe4work:latest
```

## Troubleshooting

### Common Issues

1. **Permission Errors**: Ensure the web server has write access to `storage/` and `bootstrap/cache/` directories.

2. **Database Connection**: Verify your database credentials in the `.env` file.

3. **Missing Extensions**: Install required PHP extensions using your system's package manager.

4. **Asset Issues**: Run `npm run production` to build frontend assets.

### Getting Help

- [GitHub Issues](https://github.com/Safe4Work/safe4work/issues)
- [GitHub Discussions](https://github.com/Safe4Work/safe4work/discussions)
- [Security Policy](https://github.com/Safe4Work/safe4work/security/policy)