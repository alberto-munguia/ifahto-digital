<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $proyecto_id
 * @property integer $usuario_id
 * @property string  $descripcion
 * @property string  $estatus
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class LogProyectosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('log_proyectos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // proyectos...
        $this->belongsTo('Proyectos')
            ->setForeignKey('proyecto_id')
            ->setJoinType('LEFT')
            ->setConditions(['Proyectos.estado' => 1]);

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
        $logProyecto = $this->newEntity($attributes);

        try {
            $logProyectoObj = $this->save($logProyecto);
        } catch (Exception $e) {
        }

        return $logProyectoObj == false ? $logProyectoObj : true;
    }
}
