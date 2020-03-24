<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProyectoContacto extends Entity
{
    /**
     * Tipos de contactos.
     */
    const TIPO_FACTURACION = 'Facturación';
    const TIPO_RESPONSABLE = 'Responsable';
}
