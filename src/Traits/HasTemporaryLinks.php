<?php

namespace Shahadh\TemporaryLinks\Traits;

use Shahadh\TemporaryLinks\Models\TemporaryLink;
use Shahadh\TemporaryLinks\Services\TemporaryLinkService;

trait HasTemporaryLinks
{
    /**
     * Get all temporary links for this model.
     */
    public function temporaryLinks()
    {
        return $this->morphMany(TemporaryLink::class, 'linkable');
    }

    /**
     * Create a temporary link to this model.
     */
    public function createTemporaryLink(array $options = [])
    {
        return app(TemporaryLinkService::class)->create($this, $options);
    }

    /**
     * Create a temporary link with a specific path.
     */
    public function createTemporaryLinkForPath($path, array $options = [])
    {
        $options['path'] = $path;
        return $this->createTemporaryLink($options);
    }

    /**
     * Create a single-use temporary link.
     */
    public function createSingleUseTemporaryLink(array $options = [])
    {
        $options['single_use'] = true;
        return $this->createTemporaryLink($options);
    }

    /**
     * Create a temporary link that expires after a specific time.
     */
    public function createExpiringTemporaryLink($minutes, array $options = [])
    {
        $options['expiration_minutes'] = $minutes;
        return $this->createTemporaryLink($options);
    }
}