<?php

namespace AppBundle\Controller;

use League\Fractal\Manager;
use Symfony\Component\HttpFoundation\Request;

trait FractalTrait
{
    /**
     * Return transformed model
     * @param $resource
     * @param Request $request
     * @return array
     */
    protected function fractal($resource, Request $request=null)
    {
        /** @var Manager $manager */
        $manager = $this->get('sam_j_fractal.manager');
        if ($request AND $request->query->has('include')) {
            $manager->parseIncludes($request->query->get('include'));
        }

        return $manager->createData($resource)->toArray();
    }
}