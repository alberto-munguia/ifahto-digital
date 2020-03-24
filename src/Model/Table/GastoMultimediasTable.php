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
class GastoMultimediasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('gasto_multimedias');

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
     * @param  integer        $idGasto      Id del gasto
     * @param  integer        $idMultimedia Id del multimedia
     * @return boolean|object               false|GastoMultimedia object
     */
    public function relacionarMultimedia($idGasto, $idMultimedia)
    {
        $gastoMultimediaObj = false;
        $gastoMultimedia    = $this->findByGastoIdAndMultimediaId($idGasto, $idMultimedia)->first();

        if (empty($gastoMultimedia)) {
            $gastoMultimedia = $this->newEntity();
            $gastoMultimedia->gasto_id      = $idGasto;
            $gastoMultimedia->multimedia_id = $idMultimedia;
        } else {
            $gastoMultimedia->estado = 1;
        }

        try {
            $gastoMultimediaObj = $this->save($gastoMultimedia);
        } catch (Exception $e) {
        }

        return $gastoMultimediaObj;
    }
}
