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
class FacturaMultimediasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('factura_multimedias');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // facturas...
        $this->belongsTo('Facturas')
            ->setForeignKey('factura_id')
            ->setJoinType('INNER')
            ->setConditions(['Facturas.estado' => 1]);

        // multimedias...
        $this->belongsTo('Multimedias')
            ->setForeignKey('multimedia_id')
            ->setJoinType('INNER')
            ->setConditions(['Multimedias.estado' => 1]);
    }

    /**
     * Relaciona el archivo multimedia con la factura actual.
     *
     * @param  integer        $idFactura    Id de la factura
     * @param  integer        $idMultimedia Id del multimedia
     * @return boolean|object               false|FacturaMultimedia object
     */
    public function relacionarMultimedia($idFactura, $idMultimedia)
    {
        $facturaMultimediaObj = false;
        $facturaMultimedia    = $this->findByFacturaIdAndMultimediaId($idFactura, $idMultimedia)->first();

        if (empty($facturaMultimedia)) {
            $facturaMultimedia = $this->newEntity();
            $facturaMultimedia->factura_id    = $idFactura;
            $facturaMultimedia->multimedia_id = $idMultimedia;
        } else {
            $facturaMultimedia->estado = 1;
        }

        try {
            $facturaMultimediaObj = $this->save($facturaMultimedia);
        } catch (Exception $e) {
        }

        return $facturaMultimediaObj;
    }
}
