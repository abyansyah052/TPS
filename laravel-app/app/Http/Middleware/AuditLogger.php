<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log important actions for audit trail
        if (config('security.audit.enabled', true)) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    private function logRequest(Request $request, $response)
    {
        $logData = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'timestamp' => now(),
            'user_id' => auth()->id() ?? null,
        ];

        // Log data access
        if (config('security.audit.log_data_access') && $request->is('api/*')) {
            Log::channel('audit')->info('API Access', $logData);
        }

        // Log data modifications
        if (config('security.audit.log_data_modifications') && 
            in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $logData['request_data'] = $this->sanitizeRequestData($request->all());
            Log::channel('audit')->info('Data Modification', $logData);
        }

        // Log file operations
        if (config('security.audit.log_file_operations') && 
            ($request->hasFile('file') || $request->is('*/download-*') || $request->is('*/export-*'))) {
            Log::channel('audit')->info('File Operation', $logData);
        }

        // Log admin actions
        if (config('security.audit.log_admin_actions') && $request->is('management/*')) {
            Log::channel('audit')->info('Management Action', $logData);
        }
    }

    private function sanitizeRequestData($data)
    {
        // Remove sensitive data from logs
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'api_key'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '[REDACTED]';
            }
        }

        return $data;
    }
}
