<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-Api-Key'); // atau gunakan Authorization: ApiKey <key>
        if (!$provided) {
            return response()->json(['message' => 'Missing API key'], 401);
        }

        // Cari kandidat aktif
        $clients = ApiClient::where('active', true)->get();

        foreach ($clients as $client) {
            if (Hash::check($provided, $client->key_hash)) {
                // IP allowlist (opsional)
                if ($client->ip_allowlist && !$this->ipAllowed($request->ip(), $client->ip_allowlist)) {
                    return response()->json(['message' => 'IP not allowed'], 403);
                }
                // pass client ke request
                $request->attributes->set('api_client', $client);
                return $next($request);
            }
        }

        return response()->json(['message' => 'Invalid API key'], 401);
    }

    private function ipAllowed(string $ip, array $allow): bool
    {
        foreach ($allow as $cidrOrIp) {
            if ($this->matchIpOrCidr($ip, $cidrOrIp)) return true;
        }
        return false;
    }

    private function matchIpOrCidr(string $ip, string $pattern): bool
    {
        if (!str_contains($pattern, '/')) {
            return $ip === $pattern;
        }
        // IPv4 CIDR sederhana
        [$subnet, $mask] = explode('/', $pattern);
        $mask = (int) $mask;
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1))
            === (ip2long($subnet) & ~((1 << (32 - $mask)) - 1));
    }
}
