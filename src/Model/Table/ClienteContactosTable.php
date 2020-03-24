<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property integer $id
 * @property integer $cliente_id
 * @property integer $contacto_id
 * @property string  $tipo
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ClienteContactosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('cliente_contactos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // clientes...
        $this->belongsTo('Clientes')
            ->setForeignKey('cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['Clientes.estado' => 1]);

        // contactos...
        $this->belongsTo('Contactos')
            ->setForeignKey('contacto_id')
            ->setJoinType('INNER')
            ->setConditions(['Contactos.estado' => 1]);
    }
}
