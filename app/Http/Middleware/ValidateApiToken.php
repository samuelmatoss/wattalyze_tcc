<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateApiToken
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica se é uma requisição de dispositivo IoT
        if ($request->is('api/energy-data*')) {
            return $this->handleDeviceToken($request, $next);
        }

        // Verificação para APIs externas (OAuth2 ou API tokens)
        if ($request->bearerToken()) {
            $token = $request->bearerToken();
            
            // Validação simples de token (exemplo básico)
            if (!hash_equals(config('app.api_secret'), $token)) {
                return response()->json(['error' => 'Invalid API token'], 401);
            }
            
            return $next($request);
        }

        // Verificação de assinatura HMAC para webhooks
        if ($request->hasHeader('X-Signature')) {
            $signature = $request->header('X-Signature');
            $payload = $request->getContent();
            $secret = config('app.webhook_secret');
            
            if (!$this->validateHmac($payload, $signature, $secret)) {
                Log::warning('Invalid webhook signature', [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
            
            return $next($request);
        }

        return response()->json(['error' => 'Missing authentication token'], 401);
    }

    private function handleDeviceToken(Request $request, Closure $next)
    {
        $validator = validator($request->all(), [
            'device_id' => 'required|exists:devices,id',
            'api_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $device = Device::find($request->device_id);

        if (!$device || !hash_equals($device->api_token, $request->api_token)) {
            Log::warning('Invalid device token attempt', [
                'device_id' => $request->device_id,
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Invalid device credentials'], 401);
        }

        $request->merge(['device' => $device]);

        return $next($request);
    }

    private function validateHmac(string $payload, string $signature, string $secret): bool
    {
        $computedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($signature, $computedSignature);
    }
}