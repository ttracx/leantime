# Safe4Work

A comprehensive project management and collaboration platform built with Laravel, designed to provide powerful project management capabilities with an intuitive interface.

## Features

Safe4Work offers a complete suite of project management tools including:

### Core Features

| Task Management | Project Planning | Knowledge Management | Administration |
|----------------|------------------|---------------------|----------------|
| Kanban boards, Gantt charts, table, list and calendar views | Project dashboards, reports & status updates | Wikis and documentation | Easy installation and setup |
| Unlimited subtasks and dependencies | Goal & metrics tracking | Idea boards and brainstorming | Multiple user roles and permissions |
| Milestone management | Lean & Business Model Canvas | File storage (S3 or local) | Two-factor authentication |
| Sprint management | SWOT Analysis canvas | Comments and discussions | LDAP, OIDC integration |
| Time tracking & timesheets | Risk analysis | Screen recording capabilities | Extensible via plugins and API |
| | | | Multi-language support |

### Key Capabilities

- **Project Management**: Complete project lifecycle management with agile methodologies
- **Task Organization**: Flexible task management with multiple view options
- **Team Collaboration**: Real-time collaboration tools and communication features
- **Reporting**: Comprehensive reporting and analytics dashboard
- **Integration**: Extensive third-party integrations and API support
- **Customization**: Highly customizable interface and workflow options

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

## Installation

### Quick Start with Composer

1. **Clone the repository**
   ```bash
   git clone https://github.com/Safe4Work/safe4work.git
   cd safe4work
   ```

2. **Install dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run production
   ```

3. **Configure environment**
   ```bash
   cp config/sample.env .env
   # Edit .env with your database and application settings
   ```

4. **Set up database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Set permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

6. **Access the application**
   Navigate to your web server URL and complete the setup wizard.

### Development Installation

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

### Docker Installation

```bash
# Using Docker Compose (recommended)
docker-compose up -d

# Or using Docker directly
docker run -d --name safe4work \
  -p 8080:80 \
  -e LEAN_DB_HOST=mysql_host \
  -e LEAN_DB_USER=admin \
  -e LEAN_DB_PASSWORD=secret \
  -e LEAN_DB_DATABASE=safe4work \
  safe4work/safe4work:latest
```

## Documentation

- [Installation Guide](docs/README.md) - Detailed installation instructions
- [User Manual](docs/) - Complete user documentation
- [API Documentation](docs/api/) - REST API reference
- [Developer Guide](docs/development/) - Contributing and development setup

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details on how to:

- Report bugs and request features
- Submit pull requests
- Follow our coding standards
- Join our development community

## Support

- **Issues**: [GitHub Issues](https://github.com/Safe4Work/safe4work/issues)
- **Discussions**: [GitHub Discussions](https://github.com/Safe4Work/safe4work/discussions)
- **Security**: [Security Policy](SECURITY.md)

## License

This project is licensed under the AGPL-3.0 License - see the [LICENSE](LICENSE) file for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.