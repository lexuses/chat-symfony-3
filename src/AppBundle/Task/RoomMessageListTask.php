<?php

namespace AppBundle\Task;

use AppBundle\Entity\Room;
use AppBundle\Entity\RoomMessage;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RoomMessageListTask
{
    protected $doctrine;

    /**
     * RoomMessageListTask constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    /**
     * Get list of messages by room
     *
     * @param Room $room
     * @param User $user
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run(Room $room, User $user, $limit = 10, $offset = 0)
    {
        $repository = $this->doctrine->getRepository(RoomMessage::class);

        $repository->readMessagesInChat($room, $user);

        return $repository->getMessageInRoom($room, $user, $limit, $offset);
    }
}