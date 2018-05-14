<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use BroadcastBundle\Event\BroadcastEvent;
use AppBundle\Handler\RoomMessageHandler;
use AppBundle\Transformer\RoomMessageTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RoomMessageController extends Controller
{
    use ValidationErrorTrait, FractalTrait;

    /**
     * @Route("/api/rooms/{room}/messages/", methods={"GET"}, name="room_message_list")
     */
    public function indexAction(Room $room, Request $request, RoomMessageHandler $handler)
    {
        $limit = $request->get('limit') ?? 10;
        $offset = $request->get('offset') ?? 0;

        $messages = $handler->list($room, $this->getUser(), $limit, $offset);

        $resource = new Collection($messages, new RoomMessageTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }

    /**
     * @Route("/api/rooms/{room}/messages/", methods={"POST"}, name="room_message_create")
     */
    public function createAction(Room $room, Request $request, RoomMessageHandler $handler)
    {
        if (!in_array($this->getUser(), $room->getUsers()->toArray())) {
            throw $this->createNotFoundException();
        }

        $message = $handler->save(new Message(), $room, $this->getUser(), $request);

        if($handler->getErrors()->count() > 0) {
            return $this->validationError($handler->getErrors());
        }

        $event = new BroadcastEvent($message);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch('broadcast.message', $event);

        $resource = new Item($message, new RoomMessageTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }
}