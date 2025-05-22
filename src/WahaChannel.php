<?php

namespace NotificationChannels\Waha;

use Illuminate\Notifications\Notification;
use NotificationChannels\Waha\Exceptions\CouldNotSendNotification;

class WahaChannel
{
    /** @var \NotificationChannels\Waha\WahaApi */
    protected $waha;

    public function __construct(WahaApi $waha)
    {
        $this->waha = $waha;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @throws  \NotificationChannels\Waha\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('waha');

        if (empty($to)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toWaha($notifiable);

        if (is_string($message)) {
            $message = new WahaMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    protected function sendMessage($recipient, WahaMessage $message)
    {
        $message->content = html_entity_decode($message->content, ENT_QUOTES, 'utf-8');
        $message->content = urlencode($message->content);

        //clean the recipient
        $recipient = str_replace("-", "", $recipient);
        $recipient = str_replace(" ", "", $recipient);

        $valid_mobile = '';

        //debug mode is to avoid send whatsapp to your real customer
        if ($this->waha->isDebug)
        {
            $valid_mobile = $this->waha->debugReceiveNumber;
        }
        else
        {
            if($this->waha->isMalaysiaMode)
            {
                //this is for malaysia number use case,
                if ($recipient[0] == '6')
                {
                    $valid_mobile =  $recipient;
                }

                if ($recipient[0] == '0')
                {
                    $valid_mobile = '6' . $recipient;
                }

                if ($recipient[0] == '+')
                {
                    //remove the + sign 
                    $valid_mobile = substr($recipient, 1);
                }
            }
            else
            {
                //please set [CountryCode]
                $valid_mobile = $recipient;
                if ($recipient[0] == '+')
                {
                    //remove the + sign 
                    $valid_mobile = substr($recipient, 1);
                }
            }
        }

        $params = [
            'to'        => $valid_mobile,
            'mesg'      => $message->content,
        ];

        if ($message->sendAt instanceof \DateTimeInterface) {
            $params['time'] = '0'.$message->sendAt->getTimestamp();
        }

        $this->waha->send($params);
    }
}
