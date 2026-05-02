<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Organization;

class OrganizationRejected extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Organization $organization,
        public readonly string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on Your Organization Registration — ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for registering **' . $this->organization->name . '** on our platform.')
            ->line('After reviewing your application, we are unable to approve it at this time.')
            ->line('**Reason:** ' . $this->reason)
            ->line('If you believe this is an error or would like to provide additional information, please contact us.')
            ->action('Contact Support', url('/'))
            ->line('Thank you for your interest in ' . config('app.name') . '.');
    }
}
