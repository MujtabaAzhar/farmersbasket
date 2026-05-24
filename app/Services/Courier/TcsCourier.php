<?php

namespace App\Services\Courier;

use App\Models\Shipment;

/**
 * TCS Couriers integration.
 * API docs: https://api.tcscourier.com (REST, requires TCS merchant account)
 *
 * Set courier_services.api_key    = your TCS API key / username
 * Set courier_services.api_password = your TCS password
 */
class TcsCourier extends BaseCourier
{
    public function getName(): string { return 'TCS Couriers'; }
    public function getCode(): string { return 'tcs'; }

    public function book(Shipment $shipment): array
    {
        if (!$this->isConfigured()) {
            return $this->manualBooking($shipment);
        }

        $payload = [
            'Username'          => $this->model->api_key,
            'Password'          => $this->model->api_password,
            'ReceiverName'      => $shipment->recipient_name,
            'ReceiverAddress'   => $shipment->recipient_address,
            'DestinationCityName' => $shipment->destination_city,
            'ReceiverMobile'    => $shipment->recipient_phone,
            'Weight'            => $shipment->weight,
            'NoOfPieces'        => $shipment->pieces,
            'CODAmount'         => $shipment->declared_value,
            'ReferenceNumber'   => (string) $shipment->order_id,
            'Remarks'           => $shipment->special_instructions ?? '',
        ];

        try {
            $baseUrl = $this->model->api_base_url ?: 'https://api.tcscourier.com/production/v1';
            $res = $this->http()
                ->withBasicAuth($this->model->api_key, $this->model->api_password)
                ->post("{$baseUrl}/cod/shipments", $payload);

            $body = $res->json();
            $this->logCall('book', $payload, $body ?? []);

            if ($res->successful() && !empty($body['ConsignmentNo'])) {
                return [
                    'success'         => true,
                    'tracking_number' => (string) $body['ConsignmentNo'],
                    'cn_number'       => (string) $body['ConsignmentNo'],
                    'raw'             => $body,
                ];
            }

            return [
                'success' => false,
                'message' => $body['Message'] ?? 'Booking failed.',
                'raw'     => $body ?? [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'raw' => []];
        }
    }

    public function track(string $trackingNumber): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API not configured.', 'events' => []];
        }

        try {
            $baseUrl = $this->model->api_base_url ?: 'https://api.tcscourier.com/production/v1';
            $res = $this->http()
                ->withBasicAuth($this->model->api_key, $this->model->api_password)
                ->get("{$baseUrl}/cod/shipments/{$trackingNumber}/tracking");

            $body = $res->json();
            $this->logCall('track', ['tracking_number' => $trackingNumber], $body ?? []);

            $events = [];
            foreach (($body['TrackingHistory'] ?? []) as $event) {
                $events[] = $this->normalizeEvent(
                    $event,
                    $this->mapTcsStatus($event['Status'] ?? ''),
                    $event['StatusDescription'] ?? '',
                    $event['Location'] ?? null,
                    $event['DateTime'] ?? now()->toDateTimeString(),
                );
            }

            return ['success' => true, 'events' => $events, 'raw' => $body];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'events' => []];
        }
    }

    public function cancel(string $cnNumber): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $baseUrl = $this->model->api_base_url ?: 'https://api.tcscourier.com/production/v1';
            $res = $this->http()
                ->withBasicAuth($this->model->api_key, $this->model->api_password)
                ->delete("{$baseUrl}/cod/shipments/{$cnNumber}");
            return $res->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function mapTcsStatus(string $tcsStatus): string
    {
        return match (strtolower($tcsStatus)) {
            'shipment booked'       => 'booked',
            'picked up'             => 'picked_up',
            'in transit'            => 'in_transit',
            'out for delivery'      => 'out_for_delivery',
            'delivered'             => 'delivered',
            'delivery attempted'    => 'failed',
            'returned to shipper'   => 'returned',
            default                 => 'in_transit',
        };
    }

    private function manualBooking(Shipment $shipment): array
    {
        return [
            'success'         => true,
            'tracking_number' => $this->generateLocalTracking($shipment),
            'cn_number'       => '',
            'raw'             => ['note' => 'Manual booking — no API key configured'],
        ];
    }
}
