<?php

namespace App\Controller;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class EntidadFederativasController extends AppController
{
    /**
     * Devuelve todas las entidades federativas.
     *
     * @return array Json response
     */
    public function getAll()
    {
        $entidades = $this->EntidadFederativas->findAllByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($entidades)));
    }
}
