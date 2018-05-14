<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\RoomMessage;
use AppBundle\Entity\User;
use AppBundle\Form\Type\MessageType;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoomMessageHandler
{
    private $em;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var
     */
    private $errors;
    /**
     * @var FormFactoryInterface
     */
    private $form;
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * RoomHandler constructor.
     * @param ValidatorInterface $validator
     * @param RegistryInterface $doctrine
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $form
     */
    public function __construct(
        ValidatorInterface $validator,
        RegistryInterface $doctrine,
        EntityManagerInterface $em,
        FormFactoryInterface $form
    )
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->form = $form;
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add message to room
     * @param Message $message
     * @param Room $room
     * @param User $fromUser
     * @param Request $request
     * @param bool $info
     * @return RoomMessage
     */
    public function save(Message $message, Room $room, User $fromUser, Request $request, $info = false): RoomMessage
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->form->create(MessageType::class, $message);
        $form->submit($data);
        $this->errors = $this->validator->validate($message);

        if ($this->errors->count() > 0) {
            return new RoomMessage();
        }

        $this->em->persist($message);
        $this->em->flush();

        $roomMessages = $this->roomMessages($message, $room, $fromUser, $info);

        return $roomMessages[$fromUser->getId()];
    }

    /**
     * Save message to all users in room
     * @param Message $message
     * @param Room $room
     * @param $fromUser
     * @param bool $info
     * @return array
     */
    public function roomMessages(Message $message, Room $room, $fromUser, bool $info)
    {
        $roomMessages = [];
        foreach ($room->getUsers() as $toUser) {
            $roomMessages[$toUser->getId()] = $this->addRoomMessage($message, $room, $fromUser, $toUser, $info);
        }
        $this->em->flush();

        return $roomMessages;
    }

    /**
     * Save message instance
     * @param Message $message
     * @param Room $room
     * @param $fromUser
     * @param User $toUser
     * @param bool $info
     * @return RoomMessage
     */
    private function addRoomMessage(Message $message, Room $room, $fromUser, User $toUser, bool $info)
    {
        $roomMessage = new RoomMessage();
        $roomMessage->setMessage($message);
        $roomMessage->setRoom($room);
        $roomMessage->setInfo($info);
        $roomMessage->setFromUser($fromUser);
        $roomMessage->setToUser($toUser);
        $roomMessage->setStatus(RoomMessage::STATUS_SENT);
        $roomMessage->setCreatedAt(Carbon::now());
        $this->em->persist($roomMessage);

        return $roomMessage;
    }

    /**
     * Get list of messages by room
     * @param Room $room
     * @param User $user
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function list(Room $room, User $user, $limit = 10, $offset = 0)
    {
        $repository = $this->doctrine->getRepository(RoomMessage::class);

        $repository->readMessagesInChat($room, $user);

        return $repository->getMessageInRoom($room, $user, $limit, $offset);
    }
}