<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusUktBerubahMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang antrian bila gagal.
     */
    public int $tries = 3;

    public function __construct(
        public readonly string $namaPenerima,
        public readonly string $judulPeristiwa,
        public readonly string $statusBaru,
        public readonly string $tanggalPerubahan,
        public readonly string $tautanHalaman,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[SPPK-UKT UNG] '.$this->judulPeristiwa,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.status-ukt-berubah',
            with: [
                'namaPenerima' => $this->namaPenerima,
                'judulPeristiwa' => $this->judulPeristiwa,
                'statusBaru' => $this->statusBaru,
                'tanggalPerubahan' => $this->tanggalPerubahan,
                'tautanHalaman' => $this->tautanHalaman,
            ],
        );
    }
}

