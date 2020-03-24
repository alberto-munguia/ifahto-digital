<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $proyecto_id
 * @property integer $usuario_id
 * @property string  $hora_cotizada
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProyectoRecursosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('proyecto_recursos');

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

    /**
     * Agrega usuarios al proyecto actual.
     *
     * @param  integer        $idProyecto   Id del proyecto
     * @param  integer        $idUsuario    Id del usuario
     * @param  integer        $horaCotizada Horas cotizadas
     * @return boolean|object               false|ProyectoRecurso object
     */
    public function relacionarUsuario($idProyecto, $idUsuario, $horaCotizada)
    {
        $proyectoRecursoObj = false;
        $proyectoRecurso    = $this->findByProyectoIdAndUsuarioId($idProyecto, $idUsuario)->first();

        if (empty($proyectoRecurso)) {
            $proyectoRecurso = $this->newEntity();
            $proyectoRecurso->proyecto_id   = $idProyecto;
            $proyectoRecurso->usuario_id    = $idUsuario;
            $proyectoRecurso->hora_cotizada = $horaCotizada;

            try {
                $proyectoRecursoObj = $this->save($proyectoRecurso);
            } catch (Exception $e) {
            }
        }

        return $proyectoRecursoObj;
    }
}
