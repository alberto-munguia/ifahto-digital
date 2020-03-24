<?php

namespace App\Controller;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class CiudadesController extends AppController
{
    /**
     * Devuelve todas las ciudades por entidad federativa.
     *
     * @param  integer $idEntidadFederativa Id entidad federativa
     * @return array                        Json response
     */
    public function getAllByEntidadFederativa($idEntidadFederativa)
    {
        $ciudades = $this->Ciudades
            ->findAllByEntidadFederativaIdAndEstado($idEntidadFederativa, 1)
            ->order('nombre asc')
            ->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($ciudades));
    }
}
