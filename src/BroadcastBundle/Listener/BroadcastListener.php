<?php

namespace BroadcastBundle\Listener;

use BroadcastBundle\Event\BroadcastEvent;
use BroadcastBundle\Service\PusherInterface;
use AppBundle\Transformer\RoomMessageTransformer;
use League\Fractal\Resource\Item;
use SamJ\FractalBundle\ContainerAwareManager;

class BroadcastListener
{
    /**
     * @var PusherInterface
     */
    private $pusher;
    /**
     * @var ContainerAwareManager
     */
    private $manager;

    /**
     * BroadcastListener constructor.
     * @param PusherInterface $pusher
     */
    public function __construct(PusherInterface $pusher)
    {
        $this->pusher = $pusher;
        $this->manager = new ContainerAwareManager();
    }

    public function handle(BroadcastEvent $event)
    {
        $room = $event->message->getRoom();
        $users = $room->getUsers();
        $resource = new Item($event->message, new RoomMessageTransformer());
        $data = $this->manager->createData($resource)->toArray();

        foreach ($users as $user) {
            $this->pusher->chat($user->getId(), $data);
        }
    }
}