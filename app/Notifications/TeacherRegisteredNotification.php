<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherRegisteredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly string $password) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orgName = $notifiable->organization?->name ?? config('app.name');

        return (new MailMessage)
            ->subject('🔑 Welcome as a Teacher at ' . $orgName . ' — ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been registered as a Teacher for **' . $orgName . '** on the Regenerative Systems platform.')
            ->line('Here are your account details and login credentials:')
            ->line('**Email Address:** ' . $notifiable->email)
            ->line('**Password:** ' . $this->password)
            ->action('Login to Portal', route('login'))
            ->line('For security, please log in and change your password as soon as possible.');
    }
}
