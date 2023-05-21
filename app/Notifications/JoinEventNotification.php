<?php

namespace App\Notifications;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JoinEventNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $evento;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Evento $evento)
    {
        $this->user = $user;
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
            'user_id' => $this->user->id,
            'user_nombre' => $this->user->nombre,
            'evento_id' => $this->evento->id,
            'evento_titulo' => $this->evento->titulo,
            'mensaje' => 'El usuario ' . $this->user->nombre . ' ha solicitado unirse al evento ' . $this->evento->titulo,
        ];
    }
}
