<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConvertResponseKeysToCamelCase
{
    /**
     * Handle an incoming request and convert JSON response keys to camelCase.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If it's a JsonResponse, operate on the data array
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $converted = $this->convertKeys($data);
            $response->setData($converted);
            return $response;
        }

        // For other responses with JSON content-type, try to decode and convert
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && str_contains($contentType, 'application/json')) {
            $content = $response->getContent();
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $converted = $this->convertKeys($decoded);
                $response->setContent(json_encode($converted, JSON_UNESCAPED_UNICODE));
                $response->headers->set('Content-Type', 'application/json');
            }
        }

        return $response;
    }

    /**
     * Recursively convert array/object keys to camelCase.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function convertKeys($value)
    {
        if (is_array($value)) {
            // Determine if associative array
            if ($this->isAssoc($value)) {
                $result = [];
                foreach ($value as $k => $v) {
                    $result[$this->toCamel($k)] = $this->convertKeys($v);
                }
                return $result;
            }

            // Sequential array
            $result = [];
            foreach ($value as $v) {
                $result[] = $this->convertKeys($v);
            }
            return $result;
        }

        if ($value instanceof \stdClass) {
            return $this->convertKeys((array) $value);
        }

        return $value;
    }

    protected function toCamel(string $string): string
    {
        $str = str_replace('_', ' ', $string);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);
        return lcfirst($str);
    }

    protected function isAssoc(array $arr): bool
    {
        if (empty($arr)) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
