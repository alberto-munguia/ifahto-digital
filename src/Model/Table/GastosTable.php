<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $proveedor_id
 * @property integer $proyecto_id
 * @property integer $recurso_id
 * @property integer $tipo_gasto_id
 * @property integer $usuario_id
 * @property string  $importe
 * @property string  $descripcion
 * @property string  $tipo_pago
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class GastosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('gastos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // proveedores...
        $this->belongsTo('Proveedores')
            ->setForeignKey('proveedor_id')
            ->setJoinType('INNER')
            ->setConditions(['Proveedores.estado' => 1]);

        // proyectos...
        $this->belongsTo('Proyectos')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);

        // tipo_gastos...
        $this->belongsTo('TipoGastos')
            ->setForeignKey('tipo_gasto_id')
            ->setJoinType('INNER')
            ->setConditions(['TipoGastos.estado' => 1]);

        // tipo de pagos...
        $this->belongsTo('TipoPagos')
            ->setForeignKey('tipo_pago_id')
            ->setJoinType('INNER')
            ->setConditions(['TipoPagos.estado' => 1]);

        // usuarios...
        $this->belongsTo('Usuarios')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Usuarios.estado' => 1]);

        /**
         * HasMany Associations
         */

        // log gastos...
        $this->hasMany('LogGastos')
            ->setForeignKey('gasto_id')
            ->setJoinType('INNER')
            ->setConditions(['LogGastos.estado' => 1]);

        /**
         * BelongsToMany Associations
         */

        // multimedias...
        $this->belongsToMany('Multimedias', [
            'foreignKey' => 'gasto_id',
            'joinTable'  => 'gasto_multimedias',
            'conditions' => ['Multimedias.estado' => 1],
        ]);
    }
}
