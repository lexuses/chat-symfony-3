<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Task\UserRegisterTask;
use AppBundle\Transformer\UserTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    use FractalTrait, ValidationErrorTrait;

    /**
     * @Route("/api/register/", methods={"POST"}, name="register")
     */
    public function registerAction(Request $request, UserRegisterTask $task)
    {
        $user = $task->run(new User(), $request);

        if($task->getErrors()->count() > 0) {
            return $this->validationError($task->getErrors());
        }

        // Generate token manually
        $jwt = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        return new JWTAuthenticationSuccessResponse($jwt);
    }

    /**
     * @Route("/api/user/", methods={"GET"}, name="profile")
     */
    public function profileAction(Request $request)
    {
        $resource = new Item($this->getUser(), new UserTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }

    /**
     * @Route("/api/users/", methods={"GET"}, name="user list")
     */
    public function listAction(Request $request)
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $resource = new Collection($users, new UserTransformer());

        return new JsonResponse(
            $this->fractal($resource, $request)
        );
    }
}