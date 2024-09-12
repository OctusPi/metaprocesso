<?php

namespace App\Mail;

use \App\Models\Process;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class ProposalRequest extends BaseMail
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Process $process, protected Supplier $supplier, protected $token, protected $code, protected $expiration)
    {
        parent::__construct();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->system . ' Solicitação de Cotação de Preço';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.changepassrequest',
            with: [
                'process' => [
                    'protocol'    => $this->process->protocol,
                    'date_ini'    => $this->process->date_hour_ini,
                    'organ'       => $this->process->organ->name,
                    'description' => $this->process->description
                ],
                'supplier' => [
                    'name' => $this->supplier->name,
                    'cnpj' => $this->supplier->cnpj,
                    'address' => $this->supplier->address
                ],
                'code_validation' => $this->code,
                'proposal_url' => $this->makeUrl([$this->proposalRoute, $this->token]),
                'system' => $this->system,
                'expiration' => $this->expiration,
                'sender' => $this->sender
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
