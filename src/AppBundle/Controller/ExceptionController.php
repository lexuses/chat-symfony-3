<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionController extends Controller
{
    /**
     * Throw JSON exception
     * @param FlattenException $exception
     * @param $logger
     * @return JsonResponse
     */
    public function showExceptionAction(FlattenException $exception, $logger)
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
            'status' => $exception->getStatusCode()
        ], $exception->getStatusCode());
    }
}