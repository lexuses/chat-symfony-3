<?php

namespace AppBundle\Transformer;

use AppBundle\Entity\Room;
use League\Fractal\TransformerAbstract;

class RoomTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['users'];

    public function transform(Room $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
        ];
    }

    public function includeUsers(Room $room)
    {
        return $this->collection($room->getUsers(), new UserTransformer());
    }
}