<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcast
{
use Dispatchable, InteractsWithSockets, SerializesModels;

public $message;
public $senderName;
public $senderType;
public $senderId;
public $receiverId; // تأكد من إضافة الخصائص المطلوبة

public function __construct($message, $senderName, $senderType, $senderId, $receiverId,$senderImage)
{
$this->message = $message;
$this->senderName = $senderName;
$this->senderType = $senderType;
$this->senderId = $senderId; // تعيين القيم للخصائص
$this->receiverId = $receiverId; // تعيين القيم للخصائص
$this->senderImage=$senderImage;
}

public function broadcastOn(): array
{
$channelName = 'conversation.' . min($this->senderId, $this->receiverId) . '.' . max($this->senderId, $this->receiverId);
return [new Channel($channelName)];
}

public function broadcastAs()
{
return 'Message';
}
}
