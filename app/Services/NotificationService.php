<?php

namespace App\Services;

use App\Mail\OrderConfirmation;
use App\Mail\OrderStatusUpdate;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    // -------------------------------------------------------------------------
    // Public triggers
    // -------------------------------------------------------------------------

    public static function orderPlaced(Order $order): void
    {
        $email = self::resolveEmail($order);
        $phone = self::resolvePhone($order);

        if ($email) {
            self::sendMail($email, new OrderConfirmation($order));
        }

        if ($phone) {
            $msg = self::buildOrderPlacedMessage($order);
            self::sendWhatsApp($phone, $msg);
        }
    }

    public static function orderStatusUpdated(Order $order): void
    {
        $email = self::resolveEmail($order);
        $phone = self::resolvePhone($order);

        if ($email) {
            self::sendMail($email, new OrderStatusUpdate($order));
        }

        if ($phone) {
            $msg = self::buildStatusUpdateMessage($order);
            self::sendWhatsApp($phone, $msg);
        }
    }

    // -------------------------------------------------------------------------
    // Email
    // -------------------------------------------------------------------------

    private static function sendMail(string $email, $mailable): void
    {
        if (!env('MAIL_NOTIFICATIONS_ENABLED', false)) {
            return;
        }

        try {
            Mail::to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::error('Order email failed: ' . $e->getMessage(), ['order_id' => $mailable->order->id ?? null]);
        }
    }

    // -------------------------------------------------------------------------
    // WhatsApp (Green-API)
    // -------------------------------------------------------------------------

    private static function sendWhatsApp(string $rawPhone, string $message): void
    {
        if (!config('services.whatsapp.enabled')) {
            return;
        }

        $instance = config('services.whatsapp.instance');
        $token    = config('services.whatsapp.token');
        $apiUrl   = rtrim(config('services.whatsapp.api_url'), '/');

        if (!$instance || !$token) {
            return;
        }

        $phone  = self::formatPhone($rawPhone);
        $chatId = $phone . '@c.us';
        $url    = "{$apiUrl}/waInstance{$instance}/sendMessage/{$token}";

        try {
            Http::timeout(10)->post($url, [
                'chatId'  => $chatId,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp notification failed: ' . $e->getMessage(), ['phone' => $rawPhone]);
        }
    }

    // -------------------------------------------------------------------------
    // Message builders
    // -------------------------------------------------------------------------

    private static function buildOrderPlacedMessage(Order $order): string
    {
        $name  = $order->name ?: 'Customer';
        $lines = [
            "Assalam-o-Alaikum {$name}! 🌿",
            "",
            "✅ *Order Confirmed!*",
            "Order#: {$order->order_number}",
            "Amount: Rs " . number_format($order->total, 0),
            "Date  : " . $order->created_at->format('d M Y, h:i A'),
            "",
        ];

        foreach ($order->orderItems as $item) {
            $variant = $item->variant_label ? " ({$item->variant_label})" : '';
            $lines[] = "• {$item->product?->name}{$variant} x{$item->quantity}";
        }

        $lines[] = "";
        $lines[] = "Thank you for shopping at Farmer's Basket!";
        $lines[] = "For support: +92 301 7147110";

        return implode("\n", $lines);
    }

    private static function buildStatusUpdateMessage(Order $order): string
    {
        $name    = $order->name ?: 'Customer';
        $status  = ucfirst($order->status);

        $statusMessages = [
            'confirmed' => "✅ Your order has been *confirmed* and is being prepared.",
            'packed'    => "📦 Your order has been *packed* and is ready for dispatch.",
            'shipped'   => "🚚 Your order is *on the way*!",
            'delivered' => "🎉 Your order has been *delivered*. Enjoy your fresh produce!",
            'canceled'  => "❌ Your order has been *canceled*.",
            'returned'  => "↩️ Your return request for this order has been *accepted*.",
        ];

        $statusLine = $statusMessages[$order->status]
            ?? "Your order status has been updated to: *{$status}*";

        $lines = [
            "Assalam-o-Alaikum {$name}! 🌿",
            "",
            "📋 *Order Update — {$order->order_number}*",
            $statusLine,
        ];

        if ($order->status === 'shipped' && $order->tracking_number) {
            $lines[] = "Tracking#: {$order->tracking_number}";
            if ($order->courier_name) {
                $lines[] = "Courier  : {$order->courier_name}";
            }
        }

        if ($order->status === 'shipped' && $order->estimated_delivery_date) {
            $lines[] = "Est. Delivery: " . \Carbon\Carbon::parse($order->estimated_delivery_date)->format('d M Y');
        }

        $lines[] = "";
        $lines[] = "Farmer's Basket — +92 301 7147110";

        return implode("\n", $lines);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private static function resolveEmail(Order $order): ?string
    {
        // Prefer the linked user's email
        if ($order->user_id && $order->relationLoaded('user') && $order->user?->email) {
            return $order->user->email;
        }
        if ($order->user_id) {
            $user = \App\Models\User::find($order->user_id);
            if ($user?->email) {
                return $user->email;
            }
        }
        return null;
    }

    private static function resolvePhone(Order $order): ?string
    {
        // Prefer order phone, fall back to user mobile
        if (!empty($order->phone)) {
            return $order->phone;
        }
        if ($order->user_id) {
            $user = \App\Models\User::find($order->user_id);
            if ($user?->mobile) {
                return $user->mobile;
            }
        }
        return null;
    }

    private static function formatPhone(string $phone): string
    {
        // Strip everything except digits
        $phone = preg_replace('/\D/', '', $phone);

        // Pakistani number: 03001234567 → 923001234567
        if (str_starts_with($phone, '0')) {
            $phone = '92' . substr($phone, 1);
        }

        // If it doesn't start with a country code, assume Pakistan
        if (!str_starts_with($phone, '92') && strlen($phone) <= 10) {
            $phone = '92' . $phone;
        }

        return $phone;
    }
}
