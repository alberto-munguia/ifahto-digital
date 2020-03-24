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
class PeriodicidadPagosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('periodicidad_pagos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // proyectos...
        $this->hasMany('Proyectos')
            ->setForeignKey('periodicidad_pago_id')
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);
    }
}
