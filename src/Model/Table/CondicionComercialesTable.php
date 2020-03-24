<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $tipo_cliente_id
 * @property integer $tipo_servicio_id
 * @property string  $firma
 * @property string  $credito
 * @property string  $dia_credito
 * @property string  $comision_minima
 * @property string  $servicio
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class CondicionComercialesTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('condicion_comerciales');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // tipos clientes...
        $this->belongsTo('TipoClientes')
            ->setForeignKey('tipo_cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['TipoClientes.estado' => 1]);

        // tipos servicios...
        $this->belongsTo('TipoServicios')
            ->setForeignKey('tipo_servicio_id')
            ->setJoinType('INNER')
            ->setConditions(['TipoServicios.estado' => 1]);
    }
}
