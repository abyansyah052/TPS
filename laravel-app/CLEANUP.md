# Files and directories to clean up before production deployment

## Debug and Development Files
- `server.log` - Development server log file
- `test_catalog.php` - Test file not needed in production
- `.phpunit.result.cache` - PHPUnit cache file
- `storage/logs/*.log` - Development log files (keep .gitkeep)

## IDE and Editor Files
- `.vscode/` - VS Code configuration
- `.idea/` - PhpStorm configuration
- `*.swp`, `*.swo` - Vim swap files

## OS-specific Files  
- `.DS_Store` - macOS folder attributes
- `Thumbs.db` - Windows thumbnail cache

## Dependency Directories (regenerate in production)
- `vendor/` - Composer dependencies
- `node_modules/` - NPM dependencies

## Cache and Temporary Files
- `bootstrap/cache/*.php` - Laravel bootstrap cache
- `storage/framework/cache/` - Application cache
- `storage/framework/sessions/` - Session files
- `storage/framework/views/` - Compiled views

## Local Configuration
- `.env` - Replace with production .env
- `.env.local`, `.env.development` - Local environment files

## Security Cleanup Commands

```bash
# Remove development files
rm -f server.log test_catalog.php .phpunit.result.cache
rm -rf .vscode .idea storage/logs/*.log

# Remove OS files
find . -name ".DS_Store" -delete
find . -name "Thumbs.db" -delete

# Remove dependencies (will be reinstalled)
rm -rf vendor node_modules

# Clear Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Set production environment
cp .env.production .env
php artisan key:generate

# Install production dependencies
composer install --no-dev --optimize-autoloader

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod 600 .env
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## File Structure After Cleanup

```
laravel-app/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/ (empty except .gitkeep files)
├── .env (production)
├── .env.production (template)
├── .gitignore.production
├── composer.json
├── composer.lock
├── DEPLOYMENT.md
├── SECURITY.md
├── README.md
└── vendor/ (will be recreated)
```

## Production Checklist

- [ ] All debug files removed
- [ ] Environment set to production
- [ ] Debug mode disabled
- [ ] Dependencies optimized for production
- [ ] File permissions set correctly
- [ ] Security middleware enabled
- [ ] SSL certificate configured
- [ ] Database secured
- [ ] Backup strategy implemented
- [ ] Monitoring configured
