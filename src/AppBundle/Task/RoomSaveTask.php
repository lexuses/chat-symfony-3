<?php

namespace AppBundle\Task;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Form\Type\RoomType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoomSaveTask
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
     * RoomSaveTask constructor.
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
     * Save room
     *
     * @param Room $room
     * @param User $user
     * @param Request $request
     * @return Room
     */
    public function run(Room $room, User $user, Request $request) : Room
    {
        $data = json_decode($request->getContent(), true);

        // Remove user himself from request data
        // Unique users in request data
        if (!empty($data['users']) AND is_array($data['users'])) {
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

        return $this->doctrine
            ->getRepository(Room::class)
            ->save($room);
    }
}