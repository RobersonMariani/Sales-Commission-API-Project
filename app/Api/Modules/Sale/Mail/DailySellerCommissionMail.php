<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Mail;

use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail diário enviado ao vendedor com o resumo de suas comissões.
 */
class DailySellerCommissionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Seller $seller,
        public readonly int $salesCount,
        public readonly float $totalValue,
        public readonly float $totalCommission,
        public readonly string $date,
    ) {}

    /**
     * Define o envelope do e-mail com o assunto.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Resumo de Vendas — {$this->date}",
        );
    }

    /**
     * Define o conteúdo do e-mail com a view utilizada.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-seller-commission',
        );
    }
}
