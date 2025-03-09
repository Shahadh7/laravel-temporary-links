<?php

namespace Shahadh\TemporaryLinks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TemporaryLink extends Model
{
    protected $fillable = [
        'token',
        'linkable_type',
        'linkable_id',
        'path',
        'ip_address',
        'device_signature',
        'single_use',
        'is_used',
        'access_count',
        'last_accessed_at',
        'expires_at',
    ];

    protected $casts = [
        'single_use' => 'boolean',
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Get the parent linkable model.
     */
    public function linkable()
    {
        return $this->morphTo();
    }

    /**
     * Determine if the link has expired.
     */
    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Determine if the link has been used and is single-use.
     */
    public function isUsed()
    {
        return $this->single_use && $this->is_used;
    }

    /**
     * Check if the link is still valid.
     */
    public function isValid()
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    /**
     * Mark the link as used.
     */
    public function markAsUsed()
    {
        $this->is_used = true;
        $this->access_count += 1;
        $this->last_accessed_at = Carbon::now();
        $this->save();
        
        return $this;
    }

    /**
     * Increment the access count.
     */
    public function incrementAccessCount()
    {
        $this->access_count += 1;
        $this->last_accessed_at = Carbon::now();
        $this->save();
        
        return $this;
    }

    /**
     * Get the URL for the temporary link.
     */
    public function getUrl()
    {
        return url(config('temporary-links.routes.prefix') . '/' . $this->token);
    }
}