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
class EntidadFederativasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('entidad_federativas');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // ciudades...
        $this->hasMany('Ciudades')
            ->setForeignKey('entidad_federativa_id')
            ->setJoinType('INNER')
            ->setConditions(['Ciudades.estado' => 1]);
    }
}
