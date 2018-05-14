<?php

namespace BroadcastBundle\Service;


interface PusherInterface
{
    /**
     * @param $channel string
     * @param $message mixed
     * @return mixed
     */
    public function chat($channel, $message);

    /**
     * @return mixed
     */
    public function getManager();
}