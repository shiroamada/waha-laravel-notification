<?php

namespace NotificationChannels\Waha\Test;

use NotificationChannels\Waha\WahaMessage;

class WahaMessageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_accept_a_content_when_constructing_a_message()
    {
        $message = new WahaMessage('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_accept_a_content_when_creating_a_message()
    {
        $message = WahaMessage::create('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_content()
    {
        $message = (new WahaMessage())->content('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_send_at()
    {
        $sendAt = date_create();
        $message = (new WahaMessage())->sendAt($sendAt);

        $this->assertEquals($sendAt, $message->sendAt);
    }
}
