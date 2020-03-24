<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property string  $nombre
 * @property string  $razon_social
 * @property string  $tipo
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProveedoresTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('proveedores');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // gastos...
        $this->hasMany('Gastos')
            ->setForeignKey('proveedor_id')
            ->setJoinType('INNER')
            ->setConditions(['Gastos.estado' => 1]);

        // licencias...
        $this->hasMany('Licencias')
            ->setForeignKey('proveedor_id')
            ->setJoinType('INNER')
            ->setConditions(['Licencias.estado' => 1]);
    }
}
