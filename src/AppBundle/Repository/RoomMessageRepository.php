<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\RoomMessage;
use AppBundle\Entity\User;
use \Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

/**
 * RoomMessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RoomMessageRepository extends EntityRepository
{
    /**
     * Return messages in the room by room and user
     *
     * @param Room $room
     * @param User $user
     * @param $limit
     * @param $offset
     * @return array
     */
    public function getMessageInRoom(Room $room, User $user, $limit, $offset)
    {
        return $this->findBy(
            // Conditions
            [
                'room' => $room->getId(),
                'toUser' => $user->getId(),
            ],
            // ORDER BY
            [ 'createdAt' => 'desc', ],
            $limit,
            $offset
        );
    }

    /**
     * Set messages as read in the room by room and user
     *
     * @param Room $room
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function readMessagesInChat(Room $room, User $user)
    {
        $expr = Criteria::expr();
        $criteria = (new Criteria())
            ->where($expr->andX(
                $expr->eq('room', $room),
                $expr->eq('status', RoomMessage::STATUS_SENT),
                $expr->orX(
                    $expr->neq('fromUser', $user),
                    $expr->isNull('fromUser')
                )
            ));

        $messages = $this->matching($criteria);

        foreach ($messages as $message) {
            /** @var RoomMessage $message */
            $message->setStatus(RoomMessage::STATUS_READ);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * Save message to all users in room
     *
     * @param Message $message
     * @param Room $room
     * @param $fromUser
     * @param bool $info
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveRoomMessages(Message $message, Room $room, $fromUser, bool $info)
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();

        $roomMessages = [];
        foreach ($room->getUsers() as $toUser) {
            $roomMessages[$toUser->getId()] = $this->getEntityManager()
                                        ->getRepository(Message::class)
                                        ->save($message, $room, $fromUser, $toUser, $info);
        }
        $this->getEntityManager()->flush();


        return $roomMessages;
    }
}
