<?php

namespace App\Services\NotificationDriver;

use Illuminate\Notifications\Notification;

class LinkedinPosterDriver
{
    public function send($notifiable, Notification $notification)
    {
        $notification->toLinkedin($notifiable);
    }
}
