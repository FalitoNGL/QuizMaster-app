<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Fields to sanitize (only user-facing text fields prone to XSS)
     * Note: Blade's {{ }} already escapes output - this is extra protection
     */
    protected $fieldsToSanitize = [
        'name',
        'email', 
        'title',
        'subject',
        'comment',
    ];

    /**
     * Fields to skip sanitization (allow any content)
     * Question content may contain code snippets, special chars, etc.
     */
    protected $skipFields = [
        'content',
        'description', 
        'options',
        'answer',
        'user_answer',
        'correct_answer',
    ];

    /**
     * Handle an incoming request
     * Simplified: Only sanitize specific risky fields, trust Laravel's Blade for output escaping
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value, $key) {
            if (!is_string($value)) {
                return;
            }

            // Skip content fields (may contain code/special chars)
            if (in_array($key, $this->skipFields)) {
                return;
            }

            // Only sanitize specific fields
            if (in_array($key, $this->fieldsToSanitize)) {
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
