<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class FileUploadSecurity
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
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Check file size
            $maxSize = config('security.upload.max_size', 10240) * 1024; // Convert KB to bytes
            if ($file->getSize() > $maxSize) {
                Log::warning('File upload rejected: file too large', [
                    'file_size' => $file->getSize(),
                    'max_size' => $maxSize,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'File size exceeds maximum allowed size of ' . config('security.upload.max_size') . 'KB'
                ], 413);
            }
            
            // Check file type
            $allowedTypes = config('security.upload.allowed_types', ['xlsx', 'xls']);
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedTypes)) {
                Log::warning('File upload rejected: invalid file type', [
                    'file_extension' => $extension,
                    'allowed_types' => $allowedTypes,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'File type not allowed. Only ' . implode(', ', $allowedTypes) . ' files are permitted.'
                ], 415);
            }
            
            // Check MIME type
            $allowedMimes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                'application/vnd.ms-excel', // .xls
                'application/excel',
                'application/x-excel',
                'application/x-msexcel'
            ];
            
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                Log::warning('File upload rejected: invalid MIME type', [
                    'mime_type' => $file->getMimeType(),
                    'allowed_mimes' => $allowedMimes,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file format detected.'
                ], 415);
            }
            
            // Rate limiting for file uploads
            $key = 'file-upload:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                Log::warning('File upload rate limit exceeded', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Too many upload attempts. Please try again later.'
                ], 429);
            }
            
            RateLimiter::hit($key, 300); // 5 minutes
            
            // Log successful file upload attempt
            Log::info('File upload security check passed', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'ip' => $request->ip()
            ]);
        }

        return $next($request);
    }
}
