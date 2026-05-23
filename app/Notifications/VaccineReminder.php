<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Leonis\Notifications\EasySms\Messages\EasySmsMessage;

class VaccineReminder extends Notification
{
    use Queueable;

    private string $petName;

    private string $recordTitle;

    private string $nextVisitDate;

    public function __construct(string $petName, string $recordTitle, string $nextVisitDate)
    {
        $this->petName = $petName;
        $this->recordTitle = $recordTitle;
        $this->nextVisitDate = $nextVisitDate;
    }

    public function via($notifiable): array
    {
        return [EasySmsChannel::class];
    }

    public function toEasySms($notifiable): EasySmsMessage
    {
        return (new EasySmsMessage)
            ->setContent("【iPet提醒】您的宠物 {$this->petName} 的 {$this->recordTitle} 将于 {$this->nextVisitDate} 到期，请及时安排。");
    }
}
