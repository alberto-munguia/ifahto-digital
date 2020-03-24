<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $entidad_federativa_id
 * @property string  $nombre
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class CiudadesTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('ciudades');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // entidades federativas...
        $this->belongsTo('EntidadFederativas')
            ->setForeignKey('entidad_federativa_id')
            ->setJoinType('INNER')
            ->setConditions(['EntidadFederativas.estado' => 1]);

        /**
         * HasMany Associations
         */

        // proyectos...
        $this->hasMany('Proyectos')
            ->setForeignKey('ciudad_id')
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);
    }
}
