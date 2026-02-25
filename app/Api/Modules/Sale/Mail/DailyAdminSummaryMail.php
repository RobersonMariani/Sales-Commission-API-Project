<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail diário enviado ao administrador com o resumo geral de vendas.
 */
class DailyAdminSummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly float $totalSales,
        public readonly int $salesCount,
        public readonly int $sellersCount,
        public readonly string $date,
    ) {}

    /**
     * Define o envelope do e-mail com o assunto.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Resumo Administrativo de Vendas — {$this->date}",
        );
    }

    /**
     * Define o conteúdo do e-mail com a view utilizada.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-admin-summary',
        );
    }
}
