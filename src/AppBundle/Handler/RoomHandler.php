<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Form\Type\RoomType;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoomHandler
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
     * Save room
     * @param Room $room
     * @param User $user
     * @param Request $request
     * @param RoomMessageHandler $messageHandler
     * @return Room
     */
    public function save(Room $room, User $user, Request $request, RoomMessageHandler $messageHandler) : Room
    {
        $newInstance = false;
        $data = json_decode($request->getContent(), true);

        if (!$room->getCreatedAt()) {
            $newInstance = true;
            $room->setCreatedAt(Carbon::now());
        }

        // Remove user himself from request data
        // Unique users in request data
        if (is_array($data['users']) AND !empty($data['users'])) {
            $data['users'] = array_unique($data['users']);

            if( ($k = array_search($user->getId(), $data['users'])) !== False ) {
                array_splice($data['users'], $k, 1);
            }
            $data['users'][] = $user->getId();
        }

        $form = $this->form->create(RoomType::class, $room);
        $form->submit($data);
        $this->errors = $this->validator->validate($room);

        if ($this->errors->count() > 0) {
            return $room;
        }

        $this->em->persist($room);
        $this->em->flush();

        if ($newInstance) {

            foreach ($room->getUsers() as $user) {
                /** @var User $user */
                $message = new Message();
                $message->setInfoText(Message::joinChat, $user->getUsername());
                $this->em->persist($message);
                $this->em->flush();

                $messageHandler->roomMessages($message, $room, Null, $info=true);
            }
        }

        return $room;
    }

    /**
     * List of rooms by user
     * @param User $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $this->doctrine
            ->getRepository(Room::class)
            ->roomsWithUser($user)
            ->getResult();
    }

    /**
     * Delete room
     * @param Room $room
     */
    public function delete(Room $room)
    {
        $this->em->remove($room);
        $this->em->flush();
    }
}