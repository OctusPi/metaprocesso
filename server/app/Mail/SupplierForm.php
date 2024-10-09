<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierForm extends BaseMail
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: "Cadastro de Fornecedor - $this->system"
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      markdown: 'mail.supplier_form',
      with: [
        'form_url' => $this->makeUrl(['new-supplier']),
        'system' => $this->system,
        'sender' => $this->sender,
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
