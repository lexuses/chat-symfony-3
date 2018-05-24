<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Task\RoomMessageListTask;
use BroadcastBundle\Event\BroadcastEvent;
use AppBundle\Task\RoomMessageSaveTask;
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
    public function indexAction(Room $room, Request $request, RoomMessageListTask $task)
    {
        $limit = $request->get('limit') ?? 10;
        $offset = $request->get('offset') ?? 0;

        $messages = $task->run($room, $this->getUser(), $limit, $offset);

        $resource = new Collection($messages, new RoomMessageTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }

    /**
     * @Route("/api/rooms/{room}/messages/", methods={"POST"}, name="room_message_create")
     */
    public function createAction(Room $room, Request $request, RoomMessageSaveTask $task)
    {
        if (!in_array($this->getUser(), $room->getUsers()->toArray())) {
            throw $this->createNotFoundException();
        }

        $message = $task->run(new Message(), $room, $this->getUser(), $request);

        if($task->getErrors()->count() > 0) {
            return $this->validationError($task->getErrors());
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