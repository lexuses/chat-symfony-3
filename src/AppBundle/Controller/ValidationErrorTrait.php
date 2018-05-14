<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ValidationErrorTrait
{
    /**
     * Return validation error response
     * @param $errors mixed
     * @return JsonResponse
     */
    protected function validationError($errors)
    {
        $errorsArr = [];
        foreach ($errors as $err) {
            $errorsArr[] = [
                'field' => $err->getPropertyPath(),
                'message' => $err->getMessage(),
            ];
        }

        return new JsonResponse([
            'errors' => $errorsArr
        ], 422);
    }
}