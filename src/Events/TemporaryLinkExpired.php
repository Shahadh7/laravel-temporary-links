<?php

namespace Shahadh\TemporaryLinks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Shahadh\TemporaryLinks\Models\TemporaryLink;

class TemporaryLinkExpired
{
    use Dispatchable, SerializesModels;

    public $link;

    /**
     * Create a new event instance.
     *
     * @param TemporaryLink $link
     */
    public function __construct(TemporaryLink $link)
    {
        $this->link = $link;
    }
}
