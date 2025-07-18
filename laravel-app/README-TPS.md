# TPS Dashboard

A modern Laravel-based dashboard application for managing material inventory and catalog with enhanced security features following ISO 27001:2013 standards.

## Features

- **Material Catalog Management**: View and filter materials by division, status, and placement
- **Material Management System**: Upload, edit, and export material data via Excel
- **Interactive Dashboard**: Real-time statistics and data visualization
- **Advanced Security**: Comprehensive security middleware and audit logging
- **File Security**: Secure Excel file upload/download with validation
- **Rate Limiting**: API and upload rate limiting for DDoS protection
- **Audit Trail**: Complete logging of user actions and data access

## Security Features

### Authentication & Authorization
- Session security with encryption and secure cookies
- CSRF protection on all forms
- Rate limiting on API endpoints and file uploads

### Data Protection
- Input validation and sanitization
- SQL injection prevention
- XSS protection with Content Security Policy
- Secure file upload with type and size validation
- Password-protected data exports

### Infrastructure Security
- Security headers (HSTS, CSP, X-Frame-Options, etc.)
- Audit logging for compliance
- Error handling without information disclosure
- Secure session management

## System Requirements

- **PHP**: 8.1 or higher
- **Laravel**: 11.x
- **MySQL**: 8.0 or higher
- **Memory**: 2GB RAM minimum
- **Storage**: 10GB available space

## Installation

### Development Setup

1. Clone the repository
```bash
git clone <repository-url>
cd tps-dashboard/laravel-app
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up database
```bash
php artisan migrate
php artisan db:seed
```

5. Start development server
```bash
php artisan serve
```

### Production Deployment

Please refer to `DEPLOYMENT.md` for comprehensive production deployment guide including:
- Server configuration
- Security hardening
- SSL/TLS setup
- Database security
- Monitoring and logging

## Configuration

### Environment Variables

Key environment variables for security:

```env
APP_ENV=production
APP_DEBUG=false
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
EXPORT_PASSWORD=your_secure_password
```

### Security Configuration

Security settings are configured in `config/security.php`:

- Content Security Policy
- File upload restrictions
- Rate limiting settings
- Password policy
- Audit logging options

## Usage

### Dashboard
- Access at `/` or `/dashboard`
- View material statistics and trends
- Quick access to all system features

### Catalog
- Access at `/catalog`
- Filter materials by division, status, placement
- Search functionality with real-time results

### Material Management
- Access at `/management`
- Upload Excel files to update material data
- Download template files
- Export data with password protection
- Edit individual material records

## File Operations

### Upload Format
Excel files (.xlsx, .xls) with columns:
- ID, Division, Material SAP, Description, Unit
- Status, System Location, Physical Location
- Placement, Quantity

### Security Restrictions
- Maximum file size: 10MB
- Allowed types: Excel files only
- Virus scanning (configurable)
- Rate limiting: 5 uploads per minute

## API Documentation

### Material API
- `GET /api/materials` - Get paginated materials
- `GET /api/stats` - Get dashboard statistics

### Management API
- `GET /api/management/materials` - Get materials for management
- `PUT /api/management/materials/{id}` - Update material

All API endpoints include:
- Rate limiting (60 requests/minute)
- CSRF protection
- Input validation
- Audit logging

## Security Compliance

This application follows ISO 27001:2013 standards including:

### Information Security Management
- Risk assessment and treatment
- Security policies and procedures
- Access control and user management
- Incident response procedures

### Technical Controls
- Encryption in transit and at rest
- Secure development practices
- Vulnerability management
- Security monitoring and logging

### Operational Controls
- Business continuity planning
- Backup and recovery procedures
- Change management
- Supplier relationship security

## Monitoring and Logging

### Log Types
- **Application Logs**: General application events
- **Security Logs**: Security-related events (failed logins, etc.)
- **Audit Logs**: User actions and data access (365-day retention)
- **File Operation Logs**: Upload/download activities

### Log Locations
- `storage/logs/laravel.log` - Application logs
- `storage/logs/security.log` - Security events
- `storage/logs/audit.log` - Audit trail
- `storage/logs/file-operations.log` - File activities

## Maintenance

### Daily Tasks
- Monitor log files for errors
- Check system performance
- Verify backup completion

### Weekly Tasks
- Review security logs
- Update system packages
- Test backup restoration

### Monthly Tasks
- Change export passwords
- Security audit
- Performance review
- Update dependencies

## Troubleshooting

### Common Issues

1. **Upload Failures**
   - Check file size (max 10MB)
   - Verify file format (Excel only)
   - Check server disk space

2. **Performance Issues**
   - Clear application cache: `php artisan cache:clear`
   - Optimize for production: `php artisan optimize`

3. **Security Alerts**
   - Check security logs: `tail -f storage/logs/security.log`
   - Review failed login attempts
   - Verify SSL certificate status

## Support

For technical support:
- Check application logs in `storage/logs/`
- Review configuration in `config/`
- Consult deployment guide in `DEPLOYMENT.md`
- Security guidelines in `SECURITY.md`

## Contributing

1. Follow Laravel coding standards
2. Include security considerations in all changes
3. Update documentation for new features
4. Add appropriate tests
5. Update security configuration if needed

## License

This project is proprietary software. All rights reserved.

## Changelog

### Version 1.0.0 (Production)
- Initial release with complete material management system
- Enhanced security features and compliance
- Comprehensive audit logging
- Production-ready deployment configuration
