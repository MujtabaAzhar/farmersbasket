<?php

namespace App\Services\Courier;

use App\Models\Shipment;

/**
 * M&P Express courier integration.
 * API docs: https://api.mpexpress.com.pk (REST)
 *
 * Set courier_services.api_key    = your M&P API key
 * Set courier_services.api_password = your M&P API secret
 */
class MnpCourier extends BaseCourier
{
    public function getName(): string { return 'M&P Express'; }
    public function getCode(): string { return 'mnp'; }

    public function book(Shipment $shipment): array
    {
        if (!$this->isConfigured()) {
            return $this->manualBooking($shipment);
        }

        $payload = [
            'pickup_address_code' => $this->model->settings['pickup_code'] ?? '',
            'information_display' => 1,
            'consignee_city_code' => $shipment->destination_city,
            'consignee_name'      => $shipment->recipient_name,
            'consignee_address'   => $shipment->recipient_address,
            'consignee_phone'     => $shipment->recipient_phone,
            'consignee_email'     => '',
            'order_id'            => (string) $shipment->order_id,
            'item_product_detail' => $shipment->special_instructions ?? 'Mango/Fruit',
            'item_quantity'       => $shipment->pieces,
            'item_insurance'      => 0,
            'payment_type'        => 'cod',
            'cod'                 => $shipment->declared_value,
            'item_price'          => $shipment->declared_value,
            'item_weight'         => $shipment->weight,
        ];

        try {
            $baseUrl = $this->model->api_base_url ?: 'https://api.mpexpress.com.pk';
            $res = $this->http()
                ->withHeaders(['Authorization' => 'Bearer ' . $this->model->api_key])
                ->post("{$baseUrl}/api/order/add", $payload);

            $body = $res->json();
            $this->logCall('book', $payload, $body ?? []);

            if ($res->successful() && !empty($body['tracking_number'])) {
                return [
                    'success'         => true,
                    'tracking_number' => (string) $body['tracking_number'],
                    'cn_number'       => (string) ($body['cn_number'] ?? $body['tracking_number']),
                    'raw'             => $body,
                ];
            }

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Booking failed.',
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
            $baseUrl = $this->model->api_base_url ?: 'https://api.mpexpress.com.pk';
            $res = $this->http()
                ->withHeaders(['Authorization' => 'Bearer ' . $this->model->api_key])
                ->get("{$baseUrl}/api/order/track/{$trackingNumber}");

            $body = $res->json();
            $this->logCall('track', ['tracking_number' => $trackingNumber], $body ?? []);

            $events = [];
            foreach (($body['tracking_events'] ?? []) as $event) {
                $events[] = $this->normalizeEvent(
                    $event,
                    $this->mapMnpStatus($event['event_code'] ?? ''),
                    $event['event_desc'] ?? '',
                    $event['city'] ?? null,
                    $event['event_date'] . ' ' . ($event['event_time'] ?? '00:00'),
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
            $baseUrl = $this->model->api_base_url ?: 'https://api.mpexpress.com.pk';
            $res = $this->http()
                ->withHeaders(['Authorization' => 'Bearer ' . $this->model->api_key])
                ->post("{$baseUrl}/api/order/cancel", ['tracking_number' => $cnNumber]);
            return $res->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function mapMnpStatus(string $code): string
    {
        return match (strtolower($code)) {
            'booked', 'bk'     => 'booked',
            'picked', 'pu'     => 'picked_up',
            'intransit', 'it'  => 'in_transit',
            'ofd'              => 'out_for_delivery',
            'dlv', 'delivered' => 'delivered',
            'rts', 'returned'  => 'returned',
            default            => 'in_transit',
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
