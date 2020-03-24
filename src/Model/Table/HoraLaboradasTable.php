<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $proyecto_id
 * @property integer $usuario_id
 * @property integer $total
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class HoraLaboradasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('hora_laboradas');

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
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);

        // usuarios...
        $this->belongsTo('Usuarios')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Usuarios.estado' => 1]);
    }
}
