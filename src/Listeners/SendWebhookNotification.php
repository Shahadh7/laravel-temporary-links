<?php

namespace Shahadh\TemporaryLinks\Listeners;

use Shahadh\TemporaryLinks\Events\TemporaryLinkAccessed;
use Shahadh\TemporaryLinks\Events\TemporaryLinkExpired;
use Shahadh\TemporaryLinks\Services\WebhookService;

class SendWebhookNotification
{
    protected WebhookService $webhookService;

    /**
     * Create the event listener.
     */
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle temporary link accessed event.
     */
    public function handleAccessed(TemporaryLinkAccessed $event)
    {
        $this->webhookService->send('accessed', $event->link);
    }

    /**
     * Handle temporary link expired event.
     */
    public function handleExpired(TemporaryLinkExpired $event)
    {
        $this->webhookService->send('expired', $event->link);
    }
}
