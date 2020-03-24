<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $factura_id
 * @property integer $multimedia_id
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class FacturacionMultimediasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('facturacion_multimedias');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // facturas...
        $this->belongsTo('Facturaciones')
            ->setForeignKey('facturacion_id')
            ->setJoinType('INNER')
            ->setConditions(['Facturaciones.estado' => 1]);

        // multimedias...
        $this->belongsTo('Multimedias')
            ->setForeignKey('multimedia_id')
            ->setJoinType('INNER')
            ->setConditions(['Multimedias.estado' => 1]);
    }

    /**
     * Relaciona el archivo multimedia con la factura actual.
     *
     * @param  integer        $idFacturacion Id de la factura
     * @param  integer        $idMultimedia  Id del multimedia
     * @return boolean|object                false|FacturacionMultimedia object
     */
    public function relacionarMultimedia($idFacturacion, $idMultimedia)
    {
        $facturacionMultimediaObj = false;
        $facturacionMultimedia    = $this->findByFacturacionIdAndMultimediaId($idFacturacion, $idMultimedia)->first();

        if (empty($facturacionMultimedia)) {
            $facturacionMultimedia = $this->newEntity();
            $facturacionMultimedia->facturacion_id = $idFacturacion;
            $facturacionMultimedia->multimedia_id  = $idMultimedia;
        } else {
            $facturacionMultimedia->estado = 1;
        }

        try {
            $facturacionMultimediaObj = $this->save($facturacionMultimedia);
        } catch (Exception $e) {
        }

        return $facturacionMultimediaObj;
    }
}
