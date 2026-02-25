<?php

namespace App\Api\Modules\Sale\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyAdminSummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly float $totalSales,
        public readonly int $salesCount,
        public readonly int $sellersCount,
        public readonly string $date,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Resumo Administrativo de Vendas — {$this->date}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-admin-summary',
        );
    }
}
