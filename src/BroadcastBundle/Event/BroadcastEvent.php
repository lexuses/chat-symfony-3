<?php

namespace BroadcastBundle\Event;

use AppBundle\Entity\RoomMessage;
use Symfony\Component\EventDispatcher\Event;

class BroadcastEvent extends Event
{
    public $message;

    /**
     * SubscribeEvent constructor.
     * @param $message
     */
    public function __construct(RoomMessage $message)
    {
        $this->message = $message;
    }
}