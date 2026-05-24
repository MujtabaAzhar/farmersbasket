<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierService extends Model
{
    protected $fillable = [
        'name', 'code', 'is_active',
        'api_key', 'api_password', 'api_base_url',
        'tracking_url_template', 'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
    ];

    protected $hidden = ['api_key', 'api_password'];

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function isConfigured(): bool
    {
        return !empty($this->api_key);
    }

    public function trackingUrl(string $trackingNumber): ?string
    {
        if (!$this->tracking_url_template) {
            return null;
        }
        return str_replace('{tracking_number}', $trackingNumber, $this->tracking_url_template);
    }
}
