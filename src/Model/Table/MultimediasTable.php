<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property string  $nombre_archivo
 * @property string  $url
 * @property string  $tipo_archivo
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class MultimediasTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('multimedias');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsToMany Associations
         */

        // facturas...
        $this->belongsToMany('Facturas', [
            'foreignKey' => 'multimedia_id',
            'joinTable'  => 'factura_multimedias',
            'conditions' => ['Facturas.estado' => 1],
        ]);

        // gastos...
        $this->belongsToMany('Gastos', [
            'foreignKey' => 'multimedia_id',
            'joinTable'  => 'gasto_multimedias',
            'conditions' => ['Gastos.estado' => 1],
        ]);

        // proyectos...
        $this->belongsToMany('Proyectos', [
            'foreignKey' => 'multimedia_id',
            'joinTable'  => 'proyecto_multimedias',
            'conditions' => ['Proyectos.estado' => 1],
        ]);
    }
}
