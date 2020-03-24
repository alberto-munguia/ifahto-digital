<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $gasto_id
 * @property integer $usuario_id
 * @property string  $descripcion
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class LogGastosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('log_gastos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // gastos...
        $this->belongsTo('Gastos')
            ->setForeignKey('gasto_id')
            ->setJoinType('LEFT')
            ->setConditions(['Gastos.estado' => 1]);

        // usuarios...
        $this->belongsTo('Usuarios')
            ->setForeignKey('usuario_id')
            ->setJoinType('LEFT')
            ->setConditions(['Usuarios.estado' => 1]);
    }

    /**
     * Guarda en log todas las acciones realizadas en los gastos.
     *
     * @param  array   $attributes Atributos
     * @return boolean             true|false
     */
    public function register(array $attributes)
    {
        $logGasto = $this->newEntity($attributes);

        try {
            $logGastoObj = $this->save($logGasto);
        } catch (Exception $e) {
        }

        return $logGastoObj == false ? $logGastoObj : true;
    }
}
