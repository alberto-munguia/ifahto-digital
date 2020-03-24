<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $proveedor_id
 * @property integer $tipo_pago_id
 * @property string  $nombre
 * @property string  $importe
 * @property string  $descripcion
 * @property string  $fecha
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class LicenciasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('licencias');

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
            ->setJoinType('LEFT')
            ->setConditions(['Proveedores.estado' => 1]);

        // tipo de pagos...
        $this->belongsTo('TipoPagos')
            ->setForeignKey('tipo_pago_id')
            ->setJoinType('LEFT')
            ->setConditions(['TipoPagos.estado' => 1]);

        // usuarios...
        $this->belongsTo('Usuarios')
            ->setForeignKey('usuario_id')
            ->setJoinType('LEFT')
            ->setConditions(['Usuarios.estado' => 1]);

        /**
         * HasMany Associations
         */

        // proyecto licencias...
        $this->hasMany('ProyectoLicencias')
            ->setForeignKey('licencia_id')
            ->setJoinType('INNER')
            ->setConditions(['ProyectoLicencias.estado' => 1]);

        // multimedias...
        $this->belongsToMany('Multimedias', [
            'foreignKey' => 'licencia_id',
            'joinTable'  => 'licencia_multimedias',
            'conditions' => ['Multimedias.estado' => 1],
        ]);
    }
}
