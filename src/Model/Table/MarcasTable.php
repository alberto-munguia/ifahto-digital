<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $cliente_id
 * @property string  $nombre
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class MarcasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('marcas');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // tipos clientes...
        $this->belongsTo('Clientes')
            ->setForeignKey('cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['Clientes.estado' => 1]);

        /**
         * HasMany Associations
         */

        // proyectos...
        $this->hasMany('Proyectos')
            ->setForeignKey('marca_id')
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);
    }
}
