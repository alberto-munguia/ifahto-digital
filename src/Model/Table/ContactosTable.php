<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * @property integer $id
 * @property string  $nombre
 * @property string  $apellido_paterno
 * @property string  $apellido_materno
 * @property string  $email
 * @property string  $telefono
 * @property string  $extension
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ContactosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('contactos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * HasMany Associations
         */

        // clientes contactos...
        $this->hasMany('ClienteContactos')
            ->setForeignKey('contacto_id')
            ->setJoinType('INNER')
            ->setConditions(['ClienteContactos.estado' => 1]);

        // proyectos contactos...
        $this->hasMany('ProyectoContactos')
            ->setForeignKey('contacto_id')
            ->setJoinType('INNER')
            ->setConditions(['ProyectoContactos.estado' => 1]);
    }

    /**
     * Busca si existe un contacto con el email proporcionado.
     *
     * @param  Query  $query   Query object
     * @param  array  $options Parametros
     * @return object          Query
     */
    public function findByEmail(Query $query, array $options)
    {
        return $query->where(['email' => $options['email']])->first();
    }
}
