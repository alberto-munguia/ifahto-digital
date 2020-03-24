<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class Facturacion extends Entity
{
    /**
     * Tipo de monedas
     */
    const MONEDA_PESO  = 'MXN';
    const MONEDA_DOLAR = 'USD';

    /**
     * Tipos de estatus
     */
    const ESTATUS_PAGADO     = 'Pagado';
    const ESTATUS_SOLICITADA = 'Solicitada';
    const ESTATUS_FACTURADA  = 'Facturada';
    const ESTATUS_ESPERA_ODC = 'Espera de ODC';
    const ESTATUS_CANCELADA  = 'Cancelada';
}
