<?php

namespace App\Services\Courier;

use App\Models\CourierService;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseCourier implements Contracts\CourierInterface
{
    protected CourierService $model;

    public function __construct(CourierService $model)
    {
        $this->model = $model;
    }

    public function isConfigured(): bool
    {
        return $this->model->isConfigured();
    }

    /** Shared HTTP client with timeout. */
    protected function http(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout(15)->acceptJson();
    }

    /** Log courier API calls for debugging. */
    protected function logCall(string $action, array $payload, array $response): void
    {
        Log::channel('daily')->info("[Courier:{$this->getCode()}] {$action}", [
            'payload'  => $payload,
            'response' => $response,
        ]);
    }

    /** Generate a local tracking number when the courier doesn't provide one immediately. */
    protected function generateLocalTracking(Shipment $shipment): string
    {
        return strtoupper($this->getCode()) . '-' . str_pad($shipment->id, 8, '0', STR_PAD_LEFT);
    }

    /** Normalize a raw courier event into our standard format. */
    protected function normalizeEvent(array $raw, string $status, string $description, ?string $location, string $eventTime): array
    {
        return [
            'status'      => $status,
            'description' => $description,
            'location'    => $location,
            'event_time'  => $eventTime,
            'raw'         => $raw,
        ];
    }
}
