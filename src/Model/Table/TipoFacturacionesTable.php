<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property string  $clave
 * @property string  $nombre
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class TipoFacturacionesTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('tipo_facturaciones');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // facturas...
        $this->hasMany('Facturas')
            ->setForeignKey('tipo_facturacion_id')
            ->setJoinType('INNER')
            ->setConditions(['Facturas.estado' => 1]);
    }
}
