<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        $status = ucfirst($this->order->status);
        return new Envelope(
            subject: "Order {$status} — {$this->order->order_number} | Farmer's Basket",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-update',
        );
    }
}
