<?php

namespace BroadcastBundle\Service;

use Pusher\Pusher;

class PusherService implements PusherInterface
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * PusherService constructor.
     * @param $config
     * @throws \Pusher\PusherException
     */
    public function __construct($config)
    {
        $this->pusher = new Pusher(
            $config['key'],
            $config['secret'],
            $config['id'],
            [ 'cluster' => $config['cluster'] ]
        );
    }

    public function getManager()
    {
        return $this->pusher;
    }

    public function chat($channel, $message)
    {
        try {
            $this->pusher->trigger('chat.'.$channel, 'new-message', [
                'data' => $message
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Message was not sent');
        }
    }
}