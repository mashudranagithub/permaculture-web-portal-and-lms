<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Organization;

class OrganizationApproved extends Notification
{
    use Queueable;

    public function __construct(public readonly Organization $organization) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Your Organization Has Been Approved — ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your organization **' . $this->organization->name . '** has been reviewed and approved.')
            ->line('You can now log in to the LMS and start managing your courses, batches, and students.')
            ->action('Login to Dashboard', route('login'))
            ->line('Welcome to the ' . config('app.name') . ' platform!');
    }
}
