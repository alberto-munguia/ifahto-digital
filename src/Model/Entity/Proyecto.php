<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class Proyecto extends Entity
{
    const ESTATUS_DESARROLLO = 'En Desarrollo';
    const ESTATUS_TERMINADO  = 'Terminado';
    const ESTATUS_FACTURADO  = 'Facturado';
    const ESTATUS_PAGADO     = 'Pagado';
    const ESTATUS_IGUALA     = 'Iguala';
    const ESTATUS_PERMANENTE = 'Permanente';

    /**
     * Setea la clave del proyecto.
     *
     * @param  string $clave Clave
     * @return string        Clave seteada
     */
    protected function _setClave($clave)
    {
        if ($this->isNew()) {
            $proyectosTable = TableRegistry::get('Proyectos');
            $query          = $proyectosTable->find();
            $proyectoObj    = $query->select(['id' => $query->func()->max('id')])->first();
            $id             = empty($proyectoObj->id) ? 1 : $proyectoObj->id + 1;

            return $clave . '-' . $id;
        } else {
            return $clave;
        }
    }
}
