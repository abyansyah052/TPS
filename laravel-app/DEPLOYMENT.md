# TPS Dashboard - Production Deployment Guide

## 1. Server Requirements

### Minimum System Requirements
- **OS**: Ubuntu 20.04 LTS or CentOS 8+
- **PHP**: 8.1 or higher
- **MySQL**: 8.0 or higher
- **Memory**: 2GB RAM minimum, 4GB recommended
- **Storage**: 10GB available space
- **Web Server**: Nginx (recommended) or Apache

### Required PHP Extensions
```bash
sudo apt install php8.1-cli php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-bcmath php8.1-intl
```

## 2. Security Hardening

### File Permissions
```bash
# Laravel application files
find /path/to/tps-dashboard -type f -exec chmod 644 {} \;
find /path/to/tps-dashboard -type d -exec chmod 755 {} \;

# Storage and cache directories
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Environment file
chmod 600 .env
chown root:root .env
```

### Directory Structure Security
```bash
# Move sensitive files outside web root
mkdir -p /var/tps-app/{storage,logs,backups}
ln -s /var/tps-app/storage /path/to/tps-dashboard/storage
ln -s /var/tps-app/logs /path/to/tps-dashboard/storage/logs
```

## 3. Web Server Configuration

### Nginx Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /path/to/tps-dashboard/public;

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security Headers (additional to Laravel middleware)
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    # Hide server information
    server_tokens off;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    limit_req_zone $binary_remote_addr zone=uploads:10m rate=5r/m;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Security
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location /api/ {
        limit_req zone=api burst=10 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /management/upload-data {
        limit_req zone=uploads burst=2 nodelay;
        client_max_body_size 11M;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Block access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ /(storage|bootstrap/cache) {
        deny all;
        access_log off;
        log_not_found off;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

## 4. Database Security

### MySQL Configuration
```sql
-- Create dedicated database and user
CREATE DATABASE tps_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tps_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';

-- Grant minimal required privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON tps_production.* TO 'tps_user'@'localhost';
FLUSH PRIVILEGES;

-- Enable SSL (if available)
SHOW VARIABLES LIKE 'have_ssl';
```

### Database Backup Strategy
```bash
#!/bin/bash
# /etc/cron.daily/tps-backup

BACKUP_DIR="/var/tps-app/backups"
DB_NAME="tps_production"
DB_USER="tps_user"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup
mysqldump -u $DB_USER -p $DB_NAME > $BACKUP_DIR/tps_backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/tps_backup_$DATE.sql

# Keep only last 30 days of backups
find $BACKUP_DIR -name "tps_backup_*.sql.gz" -mtime +30 -delete

# Log backup
echo "$(date): Database backup completed" >> /var/log/tps-backup.log
```

## 5. SSL/TLS Configuration

### Let's Encrypt (Recommended)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 6. Monitoring and Logging

### Log Rotation
```bash
# /etc/logrotate.d/tps-dashboard
/path/to/tps-dashboard/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    notifempty
    create 644 www-data www-data
    postrotate
        /usr/sbin/service php8.1-fpm reload > /dev/null
    endscript
}
```

### System Monitoring
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs fail2ban

# Configure fail2ban for Laravel
sudo tee /etc/fail2ban/jail.local << EOF
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
action = iptables-multiport[name=ReqLimit, port="http,https", protocol=tcp]
logpath = /var/log/nginx/error.log
findtime = 600
bantime = 7200
maxretry = 10
EOF
```

## 7. Performance Optimization

### PHP-FPM Configuration
```ini
; /etc/php/8.1/fpm/pool.d/tps.conf
[tps]
user = www-data
group = www-data
listen = /var/run/php/php8.1-fpm-tps.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 8
pm.max_requests = 1000

; Security
php_admin_value[expose_php] = Off
php_admin_value[allow_url_fopen] = Off
php_admin_value[allow_url_include] = Off
php_admin_value[file_uploads] = On
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 11M
php_admin_value[max_execution_time] = 30
php_admin_value[memory_limit] = 256M
```

### Laravel Optimization
```bash
# Run these commands after deployment
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Clear caches during updates
php artisan optimize:clear
```

## 8. Firewall Configuration

### UFW (Ubuntu)
```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow from your.admin.ip.address to any port 22
sudo ufw enable
```

## 9. Deployment Script

### Automated Deployment
```bash
#!/bin/bash
# deploy.sh

set -e

PROJECT_DIR="/path/to/tps-dashboard"
BACKUP_DIR="/var/tps-app/backups"

echo "Starting deployment..."

# Backup current version
tar -czf $BACKUP_DIR/tps_deployment_backup_$(date +%Y%m%d_%H%M%S).tar.gz $PROJECT_DIR

# Update application
cd $PROJECT_DIR
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations (if any)
php artisan migrate --force

# Clear and cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Restart services
sudo systemctl reload nginx
sudo systemctl reload php8.1-fpm

echo "Deployment completed successfully!"
```

## 10. Security Checklist

- [ ] Environment file secured (chmod 600)
- [ ] Database user has minimal privileges
- [ ] SSL certificate installed and configured
- [ ] Security headers enabled
- [ ] File upload restrictions implemented
- [ ] Rate limiting configured
- [ ] Fail2ban configured
- [ ] Regular backups scheduled
- [ ] Log rotation configured
- [ ] Firewall rules applied
- [ ] Server software updated
- [ ] PHP security settings applied
- [ ] Web server security headers configured
- [ ] Error reporting disabled in production
- [ ] Debug mode disabled
- [ ] Strong passwords used everywhere

## 11. Maintenance Tasks

### Daily
- Monitor log files for errors
- Check disk space
- Verify backup completion

### Weekly
- Review security logs
- Update system packages
- Test backup restoration

### Monthly
- Change export passwords
- Review user access
- Security audit
- Performance review

## Support and Documentation

For technical support, refer to:
- Laravel Documentation: https://laravel.com/docs
- Security Guide: See SECURITY.md
- Configuration: See config/security.php
