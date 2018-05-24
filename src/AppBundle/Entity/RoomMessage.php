<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoomMessage
 *
 * @ORM\Table(name="room_messages")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoomMessageRepository")
 */
class RoomMessage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room", inversedBy="messages", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;

    /**
     * @var Message
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Message", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private $message;

    /**
     * @var bool
     *
     * @ORM\Column(name="info", type="boolean", options={"default" : 0})
     */
    private $info;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="sentMessages")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
     */
    private $fromUser;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="receivedMessages")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
     */
    private $toUser;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    const STATUS_READ = 'read';
    const STATUS_SENT = 'sent';

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set message.
     *
     * @param Message $message
     *
     * @return RoomMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set info.
     *
     * @param bool $info
     *
     * @return RoomMessage
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info.
     *
     * @return bool
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set fromUser.
     *
     * @param User|null $fromUser
     *
     * @return RoomMessage
     */
    public function setFromUser($fromUser = null)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser.
     *
     * @return User|null
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set toUserId.
     *
     * @param User $toUser
     *
     * @return RoomMessage
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser.
     *
     * @return User
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return RoomMessage
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return RoomMessage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param Room $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }
}
