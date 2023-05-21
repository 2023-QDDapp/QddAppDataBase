<?php

namespace App\Notifications;

use App\Models\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AcceptedEventNotification extends Notification
{
    use Queueable;

    protected $evento;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Evento $evento)
    {
        $this->evento = $evento;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'evento_id' => $this->evento->id,
            'evento_titulo' => $this->evento->titulo,
            'mensaje' => 'Han aceptado tu solicitud en el evento ' . $this->evento->titulo,
        ];
    }
}
