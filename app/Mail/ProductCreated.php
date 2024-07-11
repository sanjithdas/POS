<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address ;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ProductCreated extends Mailable implements ShouldQueue, ShouldBeUnique
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

     /*
        Once the data has been set to a public property, it will automatically be available in your view,
     */
    public function __construct(Public Product $product, Public User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->user->email, $this->user->name),
    replyTo: [
        new Address('sanjith.das@gmail.com', 'Sanjith'),
    ],
            subject: 'Product Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.product.created',

        );
    }

    /*
        with: [
                'orderName' => $this->order->name,
                'orderPrice' => $this->order->price,
            ],
    */

    /**
     *
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
