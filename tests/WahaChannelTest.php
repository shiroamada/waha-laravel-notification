<?php

namespace NotificationChannels\Waha\Test;

use Illuminate\Notifications\Notification;
use Mockery as M;
use NotificationChannels\Waha\Exceptions\CouldNotSendNotification;
use NotificationChannels\Waha\WahaApi;
use NotificationChannels\Waha\WahaChannel;
use NotificationChannels\Waha\WahaMessage;

class WahaChannelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WahaApi
     */
    private $waha;

    /**
     * @var WahaMessage
     */
    private $message;

    /**
     * @var WahaChannel
     */
    private $channel;

    /**
     * @var \DateTime
     */
    public static $sendAt;

    public function setUp()
    {
        parent::setUp();

        $config = [
            'instanceId'=> 'instaceId',
            'token'     => 'token',
        ];

        $this->waha = M::mock(WahaApi::class, $config);
        $this->channel = new WahaChannel($this->waha);
        $this->message = M::mock(WahaMessage::class);
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->waha->shouldReceive('send')->once()
            ->with(
                [
                    'to'  => '60123456789',
                    'body'     => 'hello',
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_deferred_notification()
    {
        self::$sendAt = new \DateTime();

        $this->waha->shouldReceive('send')->once()
            ->with(
                [
                    'to'  => '60123456789',
                    'body'     => 'hello',
                    'time'    => '0'.self::$sendAt->getTimestamp(),
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotificationWithSendAt());
    }

    /** @test */
    public function it_does_not_send_a_message_when_to_missed()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send(
            new TestNotifiableWithoutRouteNotificationForSmscru(), new TestNotification()
        );
    }
}

class TestNotifiable
{
    public function routeNotificationFor()
    {
        return '0123456789';
    }
}

class TestNotifiableWithoutRouteNotificationForSmscru extends TestNotifiable
{
    public function routeNotificationFor()
    {
        return false;
    }
}

class TestNotification extends Notification
{
    public function toWaha()
    {
        return WahaMessage::create('hello');
    }
}

class TestNotificationWithSendAt extends Notification
{
    public function toWaha()
    {
        return WahaMessage::create('hello')
            ->sendAt(WahaChannelTest::$sendAt);
    }
}
