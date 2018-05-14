<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RoomMessage", mappedBy="fromUser")
     */
    protected $sentMessages;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RoomMessage", mappedBy="toUser")
     */
    protected $receivedMessages;

    /**
     * @Assert\NotBlank(message="Password field is required")
     * @Assert\Length(
     *     min=4,
     *     max=100,
     *     minMessage="Password must be at least 4 characters long.",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     */
    protected $plainPassword;

    /**
     * @Assert\NotBlank(message="Username field is required")
     * @Assert\Length(min="3", minMessage="Minimum length is 3 chars")
     */
    protected $username;

    /**
     * @Assert\NotBlank(message="Email field is required")
     * @Assert\Email()
     */
    protected $email;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }
}

