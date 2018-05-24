<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Room;
use AppBundle\Task\RoomSaveTask;
use AppBundle\Transformer\RoomTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends Controller
{
    use FractalTrait, ValidationErrorTrait;

    /**
     * @Route("/api/rooms/", methods={"GET"}, name="room_list")
     */
    public function indexAction(Request $request)
    {
        $rooms = $this->getDoctrine()
            ->getRepository(Room::class)
            ->roomsWithUser($this->getUser())
            ->getResult();

        $resource = new Collection($rooms, new RoomTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }

    /**
     * @Route("/api/rooms/{id}/", methods={"GET"}, name="room_item")
     */
    public function showAction(Room $room, Request $request)
    {
        if (!in_array($this->getUser(), $room->getUsers()->toArray())) {
            throw $this->createNotFoundException();
        }

        $resource = new Item($room, new RoomTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }

    /**
     * @Route("/api/rooms/", methods={"POST"}, name="room_create")
     */
    public function createAction(Request $request, RoomSaveTask $task)
    {
        $room = $task->run(new Room(), $this->getUser(), $request);

        if($task->getErrors()->count() > 0) {
            return $this->validationError($task->getErrors());
        }

        $resource = new Item($room, new RoomTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }

    /**
     * @Route("/api/rooms/{id}/", methods={"PUT"}, name="room_update")
     */
    public function updateAction(Room $room, Request $request, RoomSaveTask $task)
    {
        $room = $task->run($room, $this->getUser(), $request);

        if($task->getErrors()->count() > 0) {
            return $this->validationError($task->getErrors());
        }

        $resource = new Item($room, new RoomTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }

    /**
     * @Route("/api/rooms/{id}/", methods={"DELETE"}, name="room_delete")
     */
    public function deleteAction(Room $room, Request $request)
    {
        if (!in_array($this->getUser(), $room->getUsers()->toArray())) {
            throw $this->createNotFoundException();
        }

        $this->getDoctrine()
            ->getRepository(Room::class)
            ->delete($room);

        $resource = new Item($room, new RoomTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }
}
