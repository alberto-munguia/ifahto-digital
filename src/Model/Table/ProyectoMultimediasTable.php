<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $proyecto_id
 * @property integer $multimedia_id
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProyectoMultimediasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('proyecto_multimedias');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // proyectos...
        $this->belongsTo('Proyectos')
            ->setForeignKey('factura_id')
            ->setJoinType('INNER')
            ->setConditions(['Proyectos.estado' => 1]);

        // multimedias...
        $this->belongsTo('Multimedias')
            ->setForeignKey('multimedia_id')
            ->setJoinType('INNER')
            ->setConditions(['Multimedias.estado' => 1]);
    }

    /**
     * Relaciona el archivo multimedia con el proyecto actual.
     *
     * @param  integer        $idProyecto   Id del proyecto
     * @param  integer        $idMultimedia Id del multimedia
     * @return boolean|object               false|ProyectoMultimedia object
     */
    public function relacionarMultimedia($idProyecto, $idMultimedia)
    {
        $proyectoMultimediaObj = false;
        $proyectoMultimedia    = $this->findByMultimediaId($idMultimedia)->first();

        if (empty($proyectoMultimedia)) {
            $proyectoMultimedia = $this->newEntity();
            $proyectoMultimedia->proyecto_id   = $idProyecto;
            $proyectoMultimedia->multimedia_id = $idMultimedia;

            try {
                $proyectoMultimediaObj = $this->save($proyectoMultimedia);
            } catch (Exception $e) {
            }
        }

        return $proyectoMultimediaObj;
    }
}
