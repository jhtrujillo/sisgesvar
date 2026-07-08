<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\LabInventory;

class LabInventoryCriticalAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $user_name;

    public function __construct(LabInventory $item, $user_name)
    {
        $this->item = $item;
        $this->user_name = $user_name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ ALERTA: Stock Crítico - ' . $this->item->descripcion_item,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lab_inventory_critical',
        );
    }
}
