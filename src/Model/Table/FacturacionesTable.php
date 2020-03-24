<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $proyecto_id
 * @property integer $tipo_facturacion_id
 * @property integer $usuario_id
 * @property string  $numero_factura
 * @property string  $numero_proyecto_cliente
 * @property string  $descripcion
 * @property integer $orden_compra
 * @property string  $moneda
 * @property string  $importe
 * @property string  $iva
 * @property string  $total
 * @property string  $fecha_expedicion
 * @property string  $fecha_estimada_pago
 * @property string  $fecha_pago
 * @property string  $estatus
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class FacturacionesTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('facturaciones');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // clientes...
        $this->belongsTo('Proyectos')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);

        // tipos de facturaciones...
        $this->belongsTo('TipoFacturaciones')
            ->setForeignKey('tipo_facturacion_id')
            ->setJoinType('INNER')
            ->setConditions(['TipoFacturaciones.estado' => 1]);

        // usuarios...
        $this->belongsTo('Usuarios')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Usuarios.estado' => 1]);

        /**
         * HasMany Associations
         */

        // log facturas...
        $this->hasMany('LogFacturaciones')
            ->setForeignKey('facturacion_id')
            ->setJoinType('INNER')
            ->setConditions(['LogFacturaciones.estado' => 1]);

        /**
         * BelongsToMany Associations
         */

        // multimedias...
        $this->belongsToMany('Multimedias', [
            'foreignKey' => 'facturacion_id',
            'joinTable'  => 'facturacion_multimedias',
            'conditions' => ['Multimedias.estado' => 1],
        ]);
    }
}
