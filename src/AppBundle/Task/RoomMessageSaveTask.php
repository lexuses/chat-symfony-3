<?php

namespace AppBundle\Task;

use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\RoomMessage;
use AppBundle\Entity\User;
use AppBundle\Form\Type\MessageType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoomMessageSaveTask
{
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
     * RoomMessageSaveTask constructor.
     *
     * @param ValidatorInterface $validator
     * @param RegistryInterface $doctrine
     * @param FormFactoryInterface $form
     */
    public function __construct(
        ValidatorInterface $validator,
        RegistryInterface $doctrine,
        FormFactoryInterface $form
    )
    {
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
     *
     * @param Message $message
     * @param Room $room
     * @param User $fromUser
     * @param Request $request
     * @param bool $info
     * @return RoomMessage
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run(Message $message, Room $room, User $fromUser, Request $request, $info = false): RoomMessage
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->form->create(MessageType::class, $message);
        $form->submit($data);
        $this->errors = $this->validator->validate($message);

        if ($this->errors->count() > 0) {
            return new RoomMessage();
        }

        $roomMessages = $this->doctrine
            ->getRepository(RoomMessage::class)
            ->saveRoomMessages($message, $room, $fromUser, $info);

        return $roomMessages[$fromUser->getId()];
    }
}