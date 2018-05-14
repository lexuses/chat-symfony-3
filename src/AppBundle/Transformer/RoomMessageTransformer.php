<?php

namespace AppBundle\Transformer;

use AppBundle\Entity\RoomMessage;
use League\Fractal\TransformerAbstract;

class RoomMessageTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'from_user',
    ];

    protected $availableIncludes = [
        'message', 'to_user', 'read_by'
    ];

    public function transform(RoomMessage $message)
    {
        return [
            'id' => $message->getId(),
            'room' => $message->getRoom()->getId(),
            'text' => $message->getMessage()->getText(),
            'status' => $message->getStatus(),
            'info' => $message->getInfo(),
            'created_at' => $message->getCreatedAt()->getTimestamp(),
        ];
    }

    public function includeMessage(RoomMessage $message)
    {
        return $this->item($message->getMessage(), new MessageTransformer());
    }

    public function includeFromUser(RoomMessage $message)
    {
        if (!$user = $message->getFromUser()) {
            return $this->null();
        }
        return $this->item($user, new UserTransformer());
    }

    public function includeToUser(RoomMessage $message)
    {
        return $this->item($message->getToUser(), new UserTransformer());
    }
}