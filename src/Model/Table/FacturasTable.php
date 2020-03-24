<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $cliente_id
 * @property integer $tipo_facturacion_id
 * @property integer $usuario_id
 * @property string  $numero_factura
 * @property string  $clave
 * @property integer $orden_compra
 * @property string  $moneda
 * @property string  $importe
 * @property string  $iva
 * @property string  $total
 * @property string  $fecha_expedicion
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class FacturasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('facturas');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // clientes...
        $this->belongsTo('Clientes')
            ->setForeignKey('cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['Clientes.estado' => 1]);

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

        // log gastos...
        $this->hasMany('LogFacturas')
            ->setForeignKey('factura_id')
            ->setJoinType('INNER')
            ->setConditions(['LogFacturas.estado' => 1]);

        /**
         * BelongsToMany Associations
         */

        // proyectos...
        $this->belongsToMany('Proyectos', [
            'foreignKey' => 'factura_id',
            'joinTable'  => 'proyecto_facturas',
            'conditions' => ['Proyectos.estado' => 1],
        ]);

        // multimedias...
        $this->belongsToMany('Multimedias', [
            'foreignKey' => 'factura_id',
            'joinTable'  => 'factura_multimedias',
            'conditions' => ['Multimedias.estado' => 1],
        ]);
    }
}
