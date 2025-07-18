# TPS Dashboard Security Configuration

## Environment Setup
1. Copy `.env.production` to `.env`
2. Update all placeholder values with actual production values
3. Generate new APP_KEY: `php artisan key:generate`
4. Set proper file permissions: `chmod 600 .env`

## Database Security
- Use dedicated database user with minimal privileges
- Enable SSL connections to database
- Regular database backups
- Strong password policy

## Session Security
- Enable session encryption: `SESSION_ENCRYPT=true`
- Secure cookies: `SESSION_SECURE_COOKIE=true`
- HTTP only cookies: `SESSION_HTTP_ONLY=true`
- Same site strict: `SESSION_SAME_SITE=strict`

## File Upload Security
- Limited file types: Excel files only (.xlsx, .xls)
- Maximum file size: 5MB
- Virus scanning recommended
- Store uploads outside web root

## Rate Limiting
- API endpoints: 60 requests per minute
- Export functionality: Protected with password
- Login attempts: Limited to prevent brute force

## Security Headers
- Content Security Policy (CSP)
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Strict-Transport-Security (HSTS)

## Password Policy
- Minimum 8 characters
- Must include uppercase, lowercase, numbers
- Export password should be changed regularly
- Database passwords should be strong and unique

## Monitoring & Logging
- Error logs: Production level only
- Access logs: Monitor for suspicious activity
- Failed login attempts: Log and monitor
- File upload attempts: Log and validate

## ISO 27001:2013 Compliance
- Data encryption in transit and at rest
- Access control and user management
- Incident response procedures
- Regular security assessments
- Business continuity planning
