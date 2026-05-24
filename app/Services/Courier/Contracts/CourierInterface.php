<?php

namespace App\Services\Courier\Contracts;

use App\Models\Shipment;

interface CourierInterface
{
    /** Book/register the shipment with the courier. Returns tracking_number + cn_number on success. */
    public function book(Shipment $shipment): array;

    /** Fetch live tracking events for a tracking number. Returns array of event rows. */
    public function track(string $trackingNumber): array;

    /** Cancel a previously booked shipment. */
    public function cancel(string $cnNumber): bool;

    /** Human-readable courier name. */
    public function getName(): string;

    /** Machine code (matches courier_services.code). */
    public function getCode(): string;

    /** Whether API credentials are configured and calls should be attempted. */
    public function isConfigured(): bool;
}
