<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $gasto_id
 * @property integer $multimedia_id
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class LicenciaMultimediasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('licencia_multimedias');

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
            ->setJoinType('INNER')
            ->setConditions(['Gastos.estado' => 1]);

        // multimedias...
        $this->belongsTo('Multimedias')
            ->setForeignKey('multimedia_id')
            ->setJoinType('INNER')
            ->setConditions(['Multimedias.estado' => 1]);
    }

    /**
     * Relaciona el archivo multimedia con el gasto actual.
     *
     * @param  integer        $idLicencia   Id de la licencia
     * @param  integer        $idMultimedia Id del multimedia
     * @return boolean|object               false|LicenciaMultimedia object
     */
    public function relacionarMultimedia($idLicencia, $idMultimedia)
    {
        $licenciaMultimediaObj = false;
        $licenciaMultimedia    = $this->findByLicenciaIdAndMultimediaId($idLicencia, $idMultimedia)->first();

        if (empty($licenciaMultimedia)) {
            $licenciaMultimedia = $this->newEntity();
            $licenciaMultimedia->licencia_id   = $idLicencia;
            $licenciaMultimedia->multimedia_id = $idMultimedia;
        } else {
            $licenciaMultimedia->estado = 1;
        }

        try {
            $licenciaMultimediaObj = $this->save($licenciaMultimedia);
        } catch (Exception $e) {
        }

        return $licenciaMultimediaObj;
    }
}
