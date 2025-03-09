<?php

namespace Shahadh\TemporaryLinks\Services;

use Illuminate\Support\Facades\Http;
use Shahadh\TemporaryLinks\Models\TemporaryLink;

class WebhookService
{
    /**
     * Send a webhook notification.
     */
    public function send($event, TemporaryLink $link)
    {
        if (!config('temporary-links.webhooks.enabled')) {
            return false;
        }

        $webhookUrl = config('temporary-links.webhooks.url');
        
        if (!$webhookUrl) {
            return false;
        }

        $data = [
            'event' => $event,
            'link_id' => $link->id,
            'token' => $link->token,
            'model_type' => $link->linkable_type,
            'model_id' => $link->linkable_id,
            'path' => $link->path,
            'access_count' => $link->access_count,
            'timestamp' => now()->toIso8601String(),
        ];

        // Add signature for verification
        $signature = $this->generateSignature($data);
        
        // Send HTTP request
        return Http::withHeaders([
            'X-Temporary-Link-Signature' => $signature,
        ])->post($webhookUrl, $data);
    }

    /**
     * Generate a signature for webhook verification.
     */
    protected function generateSignature($data)
    {
        $secret = config('temporary-links.webhooks.secret');
        
        if (!$secret) {
            return null;
        }
        
        return hash_hmac('sha256', json_encode($data), $secret);
    }
}