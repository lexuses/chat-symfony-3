<?php

namespace AppBundle\Handler;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $form;

    private $errors;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    public function __construct(
        FormFactoryInterface $form,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        RegistryInterface $doctrine
    )
    {
        $this->form = $form;
        $this->validator = $validator;
        $this->em = $em;
        $this->doctrine = $doctrine;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function register(User $user, Request $request)
    {
        $user->setEnabled(True);
        $data = json_decode($request->getContent(), true);
        $form = $this->form->create(UserType::class, $user);
        $form->submit($data);
        $this->errors = $this->validator->validate($user);

        if ($this->errors->count() > 0) {
            return $user;
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function list()
    {
        return $this->doctrine
            ->getRepository(User::class)
            ->findAll();
    }
}