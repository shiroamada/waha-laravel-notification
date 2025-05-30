<?php

namespace NotificationChannels\Waha;

class WahaMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    public $content = '';

    /**
     * Time of sending a message.
     *
     * @var \DateTimeInterface
     */
    public $sendAt;

    /**
     * Create a new message instance.
     *
     * @param  string $content
     *
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /**
     * @param  string  $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the time the message should be sent.
     *
     * @param  \DateTimeInterface|null  $sendAt
     *
     * @return $this
     */
    public function sendAt(\DateTimeInterface $sendAt = null)
    {
        $this->sendAt = $sendAt;

        return $this;
    }
}
