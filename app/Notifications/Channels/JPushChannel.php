<?php

declare(strict_types=1);



namespace Leven\Notifications\Channels;

use Leven\Services\Push;
use Illuminate\Notifications\Notification;

class JPushChannel
{
    protected $pushClient;

    public function __construct(Push $pushClient)
    {
        $this->pushClient = $pushClient;
    }

    /**
     * 发送给定通知.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toArray($notifiable);

        $audience = [(string) $notifiable->id];
        $extra = ['channel' => $message['channel']];
        $alert = $message['content'];

        // 将通知发送给 $notifiable 实例
        $this->pushClient->push($alert, $audience, $extra);
    }
}
