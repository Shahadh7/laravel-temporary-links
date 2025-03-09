<?php

namespace Shahadh\TemporaryLinks\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Shahadh\TemporaryLinks\Events\TemporaryLinkAccessed;
use Shahadh\TemporaryLinks\Events\TemporaryLinkExpired;
use Shahadh\TemporaryLinks\Models\TemporaryLink;

class TemporaryLinkService
{
    /**
     * Create a new temporary link.
     */
    public function create($linkable = null, array $options = [])
    {
        $token = $this->generateUniqueToken();
        
        $data = [
            'token' => $token,
            'path' => $options['path'] ?? null,
            'single_use' => $options['single_use'] ?? config('temporary-links.default_single_use'),
            'ip_address' => $options['restrict_ip'] ?? null,
            'device_signature' => $options['restrict_device'] ?? null,
            'is_used' => false,
            'access_count' => 0,
        ];

        // Set IP restriction if requested
        if (isset($options['restrict_ip'])) {
            $data['ip_address'] = $options['restrict_ip'];
        }

        // Set device restriction if requested
        if (isset($options['restrict_device']) && $options['restrict_device']) {
            $data['device_signature'] = isset($options['device_signature']) 
                ? $options['device_signature'] 
                : $this->generateDeviceSignature(request());
        }

        // Set expiration time
        if (isset($options['expiration_minutes'])) {
            $data['expires_at'] = Carbon::now()->addMinutes($options['expiration_minutes']);
        } elseif (isset($options['expires_at'])) {
            $data['expires_at'] = Carbon::parse($options['expires_at']);
        } else {
            $data['expires_at'] = Carbon::now()->addMinutes(config('temporary-links.default_expiration'));
        }

        // Associate with model if provided
        if ($linkable instanceof Model) {
            $temporaryLink = $linkable->temporaryLinks()->create($data);
        } else {
            $temporaryLink = TemporaryLink::create($data);
        }

        return $temporaryLink;
    }

    /**
     * Get a temporary link by token.
     */
    public function find($token)
    {
        return TemporaryLink::where('token', $token)->first();
    }

    /**
     * Validate and process a temporary link.
     */
    public function validateAndProcess($token, $request)
    {
        $link = $this->find($token);
        
        if (!$link) {
            return [
                'valid' => false,
                'reason' => 'Link not found',
            ];
        }

        if ($link->isExpired()) {
            event(new TemporaryLinkExpired($link));
            
            return [
                'valid' => false,
                'reason' => 'Link has expired',
            ];
        }

        if ($link->isUsed()) {
            return [
                'valid' => false,
                'reason' => 'Link has already been used',
            ];
        }

        // Validate IP if restricted
        if ($link->ip_address && $request->ip() !== $link->ip_address) {
            return [
                'valid' => false,
                'reason' => 'IP address not allowed',
            ];
        }

        // Validate device if restricted
        if ($link->device_signature && $this->generateDeviceSignature($request) !== $link->device_signature) {
            return [
                'valid' => false,
                'reason' => 'Device not allowed',
            ];
        }

        // Mark as used if single-use
        if ($link->single_use) {
            $link->markAsUsed();
        } else {
            $link->incrementAccessCount();
        }

        // Trigger accessed event (for webhooks)
        event(new TemporaryLinkAccessed($link));

        return [
            'valid' => true,
            'link' => $link,
        ];
    }

    /**
     * Generate a unique token.
     */
    protected function generateUniqueToken()
    {
        $token = Str::random(32);
        
        // Ensure token is unique
        while (TemporaryLink::where('token', $token)->exists()) {
            $token = Str::random(32);
        }
        
        return $token;
    }

    /**
     * Generate a device signature from request data.
     */
    protected function generateDeviceSignature($request)
    {
        return md5($request->userAgent() . $request->ip());
    }
}