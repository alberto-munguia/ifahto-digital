<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * @property integer $id
 * @property integer $perfil_id
 * @property integer $puesto_id
 * @property string  $nombre
 * @property string  $apellido_paterno
 * @property string  $apellido_materno
 * @property string  $email
 * @property string  $password
 * @property string  $fecha_inicio
 * @property string  $costo_hora
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class UsuariosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('usuarios');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // perfiles...
        $this->belongsTo('Perfiles')
            ->setForeignKey('perfil_id')
            ->setJoinType('INNER')
            ->setConditions(['Perfiles.estado' => 1]);

        // puestos...
        $this->belongsTo('Puestos')
            ->setForeignKey('puesto_id')
            ->setJoinType('INNER')
            ->setConditions(['Puestos.estado' => 1]);

        /**
         * HasMany Associations
         */

        // facturas...
        $this->hasMany('Facturas')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Facturas.estado' => 1]);

        // gastos...
        $this->hasMany('Gastos')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Gastos.estado' => 1]);

        // horas laboradas...
        $this->hasMany('HoraLaboradas')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['HoraLaboradas.estado' => 1]);

        // licencias...
        $this->hasMany('Licencias')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Licencias.estado' => 1]);

        // proyectos recursos...
        $this->hasMany('ProyectoRecursos')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['ProyectoRecursos.estado' => 1]);

        // timesheets...
        $this->hasMany('Timesheets')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Timesheets.estado' => 1]);
    }

    /**
     * Busca si existe un usuario con el email proporcionado.
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
