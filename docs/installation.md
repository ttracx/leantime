# Safe4Work Installation Guide

Welcome to Safe4Work! This guide will help you install and set up Safe4Work, the AI-powered project management platform designed for neurodiverse teams.

## System Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Web Server**: Apache 2.4+ or Nginx 1.18+ (IIS 10+ supported with modifications)
- **Memory**: 512MB RAM (1GB+ recommended for production)
- **Storage**: 100MB+ for application files (1GB+ recommended for production)
- **Node.js**: 16.x or higher (for asset compilation)

### Recommended Production Requirements
- **PHP**: 8.3+
- **Database**: MySQL 8.0+ with InnoDB engine
- **Memory**: 2GB+ RAM
- **Storage**: 10GB+ with SSD storage
- **Node.js**: 18.x LTS

### Required PHP Extensions

Safe4Work requires the following PHP extensions to function properly:

**Core Extensions:**
- bcmath, ctype, curl, dom, exif, fileinfo, filter, gd, hash
- ldap, mbstring, mysqli, opcache, openssl, pcntl, pcre
- pdo, phar, session, tokenizer, zip, simplexml

**AI & Advanced Features:**
- json, xml, xmlreader, xmlwriter (for AI data processing)
- imagick (optional, for advanced image processing)
- redis (optional, for caching and session storage)

## Installation Methods

### Method 1: Quick Start (Recommended for Testing)

This method is perfect for testing Safe4Work or setting up a development environment.

#### 1. Clone the Repository

```bash
git clone https://github.com/Safe4Work/safe4work.git
cd safe4work
```

#### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm install && npm run production
```

#### 3. Configure Environment

```bash
# Copy environment configuration
cp config/sample.env .env

# Edit the .env file with your settings
nano .env
```

**Important Environment Variables:**
```env
# Database Configuration
LEAN_DB_HOST=localhost
LEAN_DB_USER=your_username
LEAN_DB_PASSWORD=your_password
LEAN_DB_DATABASE=safe4work

# Application Settings
LEAN_APP_URL=http://localhost:8080
LEAN_APP_ENV=production
LEAN_APP_DEBUG=false

# AI Features (Optional)
LEAN_AI_ENABLED=true
LEAN_AI_PROVIDER=openai
LEAN_AI_API_KEY=your_ai_api_key
```

#### 4. Set Up Database

```bash
# Run database migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed
```

#### 5. Set Permissions

```bash
# Set proper permissions for Laravel
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 6. Access the Application

Navigate to your web server URL and complete the setup wizard.

### Method 2: Production Installation

For production deployments, follow these additional steps:

#### 1. Web Server Configuration

**Apache (.htaccess is included):**
```apache
<VirtualHost *:80>
    ServerName safe4work.yourdomain.com
    DocumentRoot /path/to/safe4work/public
    
    <Directory /path/to/safe4work/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name safe4work.yourdomain.com;
    root /path/to/safe4work/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### 2. SSL Certificate Setup

```bash
# Using Let's Encrypt (recommended)
certbot --apache -d safe4work.yourdomain.com
# or
certbot --nginx -d safe4work.yourdomain.com
```

#### 3. Performance Optimization

```bash
# Enable OPcache
echo "opcache.enable=1" >> /etc/php/8.2/apache2/conf.d/99-opcache.ini
echo "opcache.memory_consumption=256" >> /etc/php/8.2/apache2/conf.d/99-opcache.ini

# Set up Redis for caching (optional)
sudo apt-get install redis-server
# Add to .env: CACHE_DRIVER=redis
```

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

### Development Features

Safe4Work includes several development tools:

- **Code Quality**: PHPStan, PHPCS, and Laravel Pint for code analysis
- **Testing**: Codeception for acceptance and unit testing
- **Hot Reloading**: Laravel Mix with hot module replacement
- **Debugging**: Whoops error pages and detailed logging

### AI Development Setup

To enable AI features in development:

```bash
# Set up AI environment variables
echo "LEAN_AI_ENABLED=true" >> .env
echo "LEAN_AI_PROVIDER=openai" >> .env
echo "LEAN_AI_API_KEY=your_api_key" >> .env

# Install AI dependencies (if not already installed)
composer require inspector-apm/neuron-ai
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

### Docker Environment Variables

```env
# Database
LEAN_DB_HOST=mysql
LEAN_DB_USER=safe4work
LEAN_DB_PASSWORD=password
LEAN_DB_DATABASE=safe4work

# Application
LEAN_APP_URL=http://localhost:8080
LEAN_APP_ENV=production
LEAN_APP_DEBUG=false

# AI Features
LEAN_AI_ENABLED=true
LEAN_AI_PROVIDER=openai
LEAN_AI_API_KEY=your_api_key
```

## Kubernetes Installation

Safe4Work includes Helm charts for Kubernetes deployment:

```bash
# Add the Safe4Work Helm repository
helm repo add safe4work https://safe4work.github.io/helm-charts
helm repo update

# Install Safe4Work
helm install safe4work safe4work/safe4work \
  --set database.host=your-mysql-host \
  --set database.user=safe4work \
  --set database.password=your-password \
  --set database.database=safe4work
```

### Helm Configuration

Key configuration options:

```yaml
# values.yaml
replicaCount: 2

image:
  repository: safe4work/safe4work
  tag: "latest"
  pullPolicy: IfNotPresent

service:
  type: ClusterIP
  port: 80

ingress:
  enabled: true
  className: "nginx"
  annotations:
    cert-manager.io/cluster-issuer: "letsencrypt-prod"
  hosts:
    - host: safe4work.yourdomain.com
      paths:
        - path: /
          pathType: Prefix
  tls:
    - secretName: safe4work-tls
      hosts:
        - safe4work.yourdomain.com

database:
  host: mysql
  port: 3306
  user: safe4work
  password: password
  database: safe4work

ai:
  enabled: true
  provider: openai
  apiKey: your-api-key
```

## Post-Installation Configuration

### 1. Initial Setup Wizard

After installation, access your Safe4Work instance and complete the setup wizard:

1. **Admin Account**: Create your first admin account
2. **Organization**: Set up your organization details
3. **Preferences**: Configure accessibility and neurodiversity settings
4. **AI Setup**: Configure AI features (optional)
5. **Integrations**: Set up third-party integrations

### 2. Accessibility Configuration

Safe4Work includes extensive accessibility features:

```bash
# Enable accessibility features
php artisan config:set LEAN_ACCESSIBILITY_ENABLED=true
php artisan config:set LEAN_ACCESSIBILITY_HIGH_CONTRAST=true
php artisan config:set LEAN_ACCESSIBILITY_SCREEN_READER=true
```

### 3. AI Features Setup

Configure AI-powered features:

```bash
# Set up AI provider
php artisan config:set LEAN_AI_PROVIDER=openai
php artisan config:set LEAN_AI_API_KEY=your_api_key

# Enable specific AI features
php artisan config:set LEAN_AI_TASK_SUGGESTIONS=true
php artisan config:set LEAN_AI_WORKFLOW_AUTOMATION=true
php artisan config:set LEAN_AI_ACCESSIBILITY_ASSISTANCE=true
```

### 4. Plugin Installation

Safe4Work supports plugins for extended functionality:

```bash
# Install a plugin
composer require safe4work/plugin-name

# Enable the plugin
php artisan plugin:enable plugin-name
```

## Troubleshooting

### Common Issues

1. **Permission Errors**: 
   ```bash
   # Ensure proper permissions
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Database Connection**: 
   - Verify database credentials in `.env`
   - Ensure database server is running
   - Check firewall settings

3. **Missing PHP Extensions**: 
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php8.2-mbstring php8.2-xml php8.2-curl
   
   # CentOS/RHEL
   sudo yum install php-mbstring php-xml php-curl
   ```

4. **Asset Issues**: 
   ```bash
   # Rebuild assets
   npm run production
   
   # Clear caches
   php artisan cache:clear
   php artisan config:clear
   ```

5. **AI Features Not Working**:
   - Verify API key is correct
   - Check internet connectivity
   - Review AI provider status

### Performance Issues

1. **Slow Loading**: Enable OPcache and Redis caching
2. **Memory Issues**: Increase PHP memory limit
3. **Database Performance**: Optimize database queries and indexes

### Security Considerations

1. **Environment Security**: Never commit `.env` files
2. **Database Security**: Use strong passwords and limit access
3. **SSL/TLS**: Always use HTTPS in production
4. **Updates**: Keep Safe4Work and dependencies updated

### Getting Help

- **Documentation**: [docs.safe4work.com](https://docs.safe4work.com)
- **GitHub Issues**: [Report bugs and request features](https://github.com/Safe4Work/safe4work/issues)
- **Community**: [GitHub Discussions](https://github.com/Safe4Work/safe4work/discussions)
- **Security**: [Security Policy](https://github.com/Safe4Work/safe4work/security/policy)
- **Support**: [support@safe4work.com](mailto:support@safe4work.com)

### Log Files

Check these log files for debugging:

- **Application Logs**: `storage/logs/laravel.log`
- **Web Server Logs**: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
- **PHP Logs**: `/var/log/php/error.log`

## Next Steps

After successful installation:

1. **Explore Features**: Take a tour of Safe4Work's features
2. **Configure Teams**: Set up user accounts and teams
3. **Create Projects**: Start your first project
4. **Customize Settings**: Adjust preferences for your team's needs
5. **Install Plugins**: Extend functionality with plugins
6. **Join Community**: Connect with other Safe4Work users

Welcome to Safe4Work! 🧠✨