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
class UsuarioAreasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('usuario_areas');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // usuarios...
        $this->hasMany('Usuarios')
            ->setForeignKey('usuario_area_id')
            ->setJoinType('INNER')
            ->setConditions(['Usuarios.estado' => 1]);
    }
}
