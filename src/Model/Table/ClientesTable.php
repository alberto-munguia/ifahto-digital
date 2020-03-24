<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property string  $razon_social
 * @property string  $nombre
 * @property string  $rfc
 * @property string  $direccion
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ClientesTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('clientes');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // marcas...
        $this->hasMany('Marcas')
            ->setForeignKey('cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['Marcas.estado' => 1]);

        // proyectos...
        $this->hasMany('Proyectos')
            ->setForeignKey('cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);
    }
}
