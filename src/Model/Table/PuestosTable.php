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
class PuestosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('puestos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // areas...
        $this->belongsTo('Areas')
            ->setForeignKey('area_id')
            ->setJoinType('INNER')
            ->setConditions(['Areas.estado' => 1]);

        /**
         * HasMany Associations
         */

        // usuarios...
        $this->hasMany('Usuarios')
            ->setForeignKey('puesto_id')
            ->setJoinType('INNER')
            ->setConditions(['Usuarios.estado' => 1]);
    }
}
