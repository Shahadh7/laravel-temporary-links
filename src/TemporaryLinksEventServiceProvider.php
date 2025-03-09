<?php

namespace Shahadh\TemporaryLinks;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Shahadh\TemporaryLinks\Events\TemporaryLinkAccessed;
use Shahadh\TemporaryLinks\Events\TemporaryLinkExpired;
use Shahadh\TemporaryLinks\Listeners\SendWebhookNotification;

class TemporaryLinksEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TemporaryLinkAccessed::class => [
            SendWebhookNotification::class . '@handleAccessed',
        ],
        TemporaryLinkExpired::class => [
            SendWebhookNotification::class . '@handleExpired',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}