<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    private const SENSITIVE = ['password', 'password_confirmation', 'current_password', 'token', '_token', 'api_key', 'secret'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true) || $response->getStatusCode() >= 400) {
            return $response;
        }

        $route = $request->route();
        $parameters = $route?->parameters() ?? [];
        $subject = collect($parameters)->first(fn ($value) => is_object($value) && method_exists($value, 'getKey'));
        $values = collect($request->except(self::SENSITIVE))
            ->reject(fn ($value, $key) => str_contains(strtolower((string) $key), 'secret') || str_contains(strtolower((string) $key), 'password'))
            ->map(fn ($value) => $this->sanitize($value))
            ->all();

        try {
            AdminActivityLog::create([
                'admin_id' => $request->user()?->id,
                'action' => (string) $route?->getName(),
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'description' => $request->method().' '.$request->path(),
                'new_values' => $values,
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log admin activity', ['error' => $e->getMessage()]);
        }

        return $response;
    }

    private function sanitize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->sanitize($item), $value);
        }
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            return ['file' => $value->getClientOriginalName(), 'size' => $value->getSize()];
        }
        if (is_string($value)) {
            return mb_substr($value, 0, 1000);
        }

        return is_scalar($value) || $value === null ? $value : get_debug_type($value);
    }
}
