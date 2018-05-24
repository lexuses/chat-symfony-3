<?php

namespace AppBundle\Task;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegisterTask
{
    /**
     * @var FormFactoryInterface
     */
    private $form;
    /**
     * @var array
     */
    private $errors;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    public function __construct(
        FormFactoryInterface $form,
        ValidatorInterface $validator,
        RegistryInterface $doctrine
    )
    {
        $this->form = $form;
        $this->validator = $validator;
        $this->doctrine = $doctrine;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Register user
     *
     * @param User $user
     * @param Request $request
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run(User $user, Request $request)
    {
        $user->setEnabled(True);
        $data = json_decode($request->getContent(), true);
        $form = $this->form->create(UserType::class, $user);
        $form->submit($data);
        $this->errors = $this->validator->validate($user);

        if ($this->errors->count() > 0) {
            return $user;
        }

        $this->doctrine
            ->getRepository(User::class)
            ->save($user);

        return $user;
    }
}