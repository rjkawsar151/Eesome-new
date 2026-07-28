<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class EmailVerificationCode extends Notification
{
    use Queueable;
    public function __construct(public string $code) {}
    public function via(object $notifiable): array { return ['mail']; }
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Your EESOME verification code')->greeting('Hello '.$notifiable->name.',')->line('Use this verification code to confirm your email address:')->line($this->code)->line('This code expires in 10 minutes. If you did not create this account, you can ignore this email.');
    }
}