<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * @property integer $id
 * @property integer $proyecto_id
 * @property integer $contacto_id
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProyectoContactosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('proyecto_contactos');

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

        // contactos...
        $this->belongsTo('Contactos')
            ->setForeignKey('contacto_id')
            ->setJoinType('INNER')
            ->setConditions(['Contactos.estado' => 1]);
    }

    /**
     * Relaciona contactos con el proyecto actual.
     *
     * @param  integer $idProyecto Id del proyecto
     * @param  integer $idContacto Id del contacto
     * @param  string  $tipo       Tipo de contacto
     * @return object              Query object|false
     */
    public function relacionarContacto($idProyecto, $idContacto, $tipo)
    {
        $proyectoContacto = $this->find('all', [
            'conditions' => [
                'proyecto_id' => $idProyecto,
                'tipo'        => $tipo,
            ],
        ])->first();

        if (!empty($proyectoContacto)) {
            $proyectoContacto->estado = 1;

            if ($proyectoContacto->contacto_id != $idContacto) {
                $proyectoContacto->contacto_id = $idContacto;
            }
        } else {
            $proyectoContacto = $this->newEntity();
            $proyectoContacto->proyecto_id = $idProyecto;
            $proyectoContacto->contacto_id = $idContacto;
            $proyectoContacto->tipo        = $tipo;
        }

        try {
            $proyectoContactoObj = $this->save($proyectoContacto);
        } catch (Exception $e) {
        }

        return $proyectoContactoObj;
    }
}
