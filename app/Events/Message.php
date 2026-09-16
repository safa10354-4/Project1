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
public $sender_id;
public $sender_name;
public $sender_type;
public $sender_image;
// public $senderId;

public $receiver_id; // تأكد من إضافة الخصائص المطلوبة

public function __construct($message,$sender_name ,$sender_type ,$sender_id ,$receiver_id ,$sender_image)
{
$this->message = $message;
$this->sender_name  = $sender_name ;
$this->sender_type = $sender_type;
$this->sender_id=$sender_id ; // تعيين القيم للخصائص
$this->receiver_id = $receiver_id; // تعيين القيم للخصائص
$this->sender_image=$sender_image;
}

public function broadcastOn(): array
{
$channelName = 'conversation.' . min($this->sender_id, $this->receiver_id) . '.' . max($this->sender_id, $this->receiver_id);
return [new Channel($channelName)];
}

public function broadcastAs()
{
return 'Message';
}
}
