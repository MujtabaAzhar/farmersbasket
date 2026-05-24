<?php

namespace App\Services\Courier;

use App\Models\Shipment;

/**
 * Leopards Express courier integration.
 * API docs: https://merchantapi.leopardscourier.com
 *
 * Set courier_services.api_key    = your Leopards API key
 * Set courier_services.api_password = your Leopards API password
 */
class LeopardsCourier extends BaseCourier
{
    public function getName(): string { return 'Leopards Courier'; }
    public function getCode(): string { return 'leopards'; }

    public function book(Shipment $shipment): array
    {
        if (!$this->isConfigured()) {
            return $this->manualBooking($shipment);
        }

        $payload = [
            'api_key'            => $this->model->api_key,
            'api_password'       => $this->model->api_password,
            'booked_packet_weight'       => $shipment->weight,
            'booked_packet_no_piece'     => $shipment->pieces,
            'booked_packet_collect_amount' => $shipment->declared_value,
            'booked_packet_order_id'     => (string) $shipment->order_id,
            'shipment_name_eng'          => $shipment->recipient_name,
            'shipment_phone'             => $shipment->recipient_phone,
            'shipment_address'           => $shipment->recipient_address,
            'shipment_city'              => $shipment->destination_city,
            'shipment_country'           => 'Pakistan',
            'special_instructions'       => $shipment->special_instructions ?? '',
        ];

        try {
            $baseUrl = $this->model->api_base_url ?: 'https://merchantapi.leopardscourier.com';
            $res = $this->http()->post("{$baseUrl}/api/createShipment/format/json/", $payload);
            $body = $res->json();
            $this->logCall('book', $payload, $body ?? []);

            if (($body['status'] ?? 0) == 1) {
                return [
                    'success'         => true,
                    'tracking_number' => (string) ($body['track_number'] ?? $this->generateLocalTracking($shipment)),
                    'cn_number'       => (string) ($body['packet_cn'] ?? ''),
                    'raw'             => $body,
                ];
            }

            return [
                'success' => false,
                'message' => $body['error'] ?? 'Booking failed.',
                'raw'     => $body,
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

        $payload = [
            'api_key'      => $this->model->api_key,
            'api_password' => $this->model->api_password,
            'track_numbers' => $trackingNumber,
        ];

        try {
            $baseUrl = $this->model->api_base_url ?: 'https://merchantapi.leopardscourier.com';
            $res = $this->http()->post("{$baseUrl}/api/trackBookedPackets/format/json/", $payload);
            $body = $res->json();
            $this->logCall('track', $payload, $body ?? []);

            $events = [];
            foreach (($body['packet_list'] ?? []) as $pkt) {
                foreach (($pkt['activity_info'] ?? []) as $act) {
                    $events[] = $this->normalizeEvent(
                        $act,
                        $act['CN_STATUS'] ?? 'in_transit',
                        $act['ACTIVITY'] ?? '',
                        $act['CITY'] ?? null,
                        $act['ACT_DATE'] . ' ' . ($act['ACT_TIME'] ?? '00:00'),
                    );
                }
            }

            return ['success' => true, 'events' => $events, 'raw' => $body];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'events' => []];
        }
    }

    public function cancel(string $cnNumber): bool
    {
        // Leopards does not support API cancellation — handle manually
        return false;
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
