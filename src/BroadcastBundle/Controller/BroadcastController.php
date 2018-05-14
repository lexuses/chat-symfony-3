<?php

namespace BroadcastBundle\Controller;

use AppBundle\Entity\User;
use BroadcastBundle\Service\PusherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BroadcastController extends Controller
{
    /**
     * @Route("/api/broadcast/auth/", methods={"POST"})
     */
    public function authAction(PusherInterface $pusher, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $socketId = $request->get('socket_id');
        $channelName = $request->get('channel_name');

        if(!$user OR $channelName != 'chat.'.$user->getId()) {
            throw new AccessDeniedException('Request authentication denied');
        }

        $data = $pusher->getManager()->socket_auth($channelName, $socketId);
        $data['channel_name'] = $channelName;

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/pusher/", methods={"POST"})
     */
    public function pushAction()
    {
        $pusher = $this->get(PusherInterface::class);

        $pusher->chat('test', [
            'message' => 'hello world'
        ]);

        return new JsonResponse(
            ['Test message was sent']
        );
    }
}