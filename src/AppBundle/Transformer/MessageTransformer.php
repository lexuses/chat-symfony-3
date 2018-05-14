<?php

namespace AppBundle\Transformer;

use AppBundle\Entity\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    public function transform(Message $message)
    {
        return [
            'id' => $message->getId(),
            'text' => $message->getText(),
        ];
    }
}