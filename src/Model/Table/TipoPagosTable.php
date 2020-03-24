<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property string  $nombre
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class TipoPagosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('tipo_pagos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // gastos...
        $this->hasMany('Gastos')
            ->setForeignKey('tipo_pago_id')
            ->setJoinType('INNER')
            ->setConditions(['Gastos.estado' => 1]);

        // licencias...
        $this->hasMany('Licencias')
            ->setForeignKey('tipo_pago_id')
            ->setJoinType('INNER')
            ->setConditions(['Licencias.estado' => 1]);
    }
}
