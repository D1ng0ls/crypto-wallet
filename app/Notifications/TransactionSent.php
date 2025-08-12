<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;

class TransactionSent extends Notification implements ShouldQueue
{
    use Queueable;

    public Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nova Transação Realizada!')
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Você realizou uma nova transação.')
            ->line('Valor: ' . $this->transaction->amount . ' ' . $this->transaction->coin->symbol)
            ->action('Ver Minha Carteira', url(route('dashboard')))
            ->line('Obrigado por usar nossa plataforma!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'coin_name' => $this->transaction->coin->name,
            'type' => $this->transaction->type,
            'message' => "Você realizou uma transferência de {$this->transaction->amount} {$this->transaction->coin->name} para {$this->transaction->receiver->name}."
        ];
    }
}
