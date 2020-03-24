<?php

namespace App\Controller;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class PerfilesController extends AppController
{
    public function getAll()
    {
        $perfiles = $this->Perfiles->findAllByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($perfiles)));
    }
}