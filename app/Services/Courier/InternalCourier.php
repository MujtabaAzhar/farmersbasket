<?php

namespace App\Services\Courier;

use App\Models\Shipment;

/**
 * Farmer's Basket internal delivery driver.
 * No external API — generates FB-YYYY-XXXX tracking numbers and
 * relies on manual status updates by the assigned rider/admin.
 */
class InternalCourier extends BaseCourier
{
    public function getName(): string { return "Farmer's Basket Delivery"; }
    public function getCode(): string { return 'internal'; }

    /** Internal delivery is always "configured" — no external credentials needed. */
    public function isConfigured(): bool { return true; }

    public function book(Shipment $shipment): array
    {
        $tracking = $this->generateTrackingNumber($shipment);

        return [
            'success'         => true,
            'tracking_number' => $tracking,
            'cn_number'       => $tracking,
            'raw'             => ['type' => 'internal', 'generated_at' => now()->toIso8601String()],
        ];
    }

    /** Internal tracking is always pulled from our own DB — no API call needed. */
    public function track(string $trackingNumber): array
    {
        return ['success' => true, 'events' => [], 'raw' => []];
    }

    public function cancel(string $cnNumber): bool { return true; }

    private function generateTrackingNumber(Shipment $shipment): string
    {
        return 'FB-' . date('Y') . '-' . str_pad($shipment->id, 4, '0', STR_PAD_LEFT);
    }
}
