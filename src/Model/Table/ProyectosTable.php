<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use App\Model\Entity\Proyecto;
use ArrayObject;

/**
 * @property integer $id
 * @property integer $ciudad_id
 * @property integer $cliente_id
 * @property integer $marca_id
 * @property integer $usuario_id
 * @property integer $periodicidad_pago_id
 * @property integer $tipo_cliente_id
 * @property integer $tipo_servicio_id
 * @property string  $clave
 * @property string  $nombre
 * @property string  $descripcion
 * @property string  $cliente_autorizacion
 * @property string  $responsable
 * @property integer $requiere_anticipo
 * @property string  $anticipo
 * @property integer $porcentaje_anticipo
 * @property string  $monto
 * @property integer $numero_pago
 * @property string  $estatus
 * @property string  $entrega
 * @property string  $finalizacion
 * @property string  $fecha_autorizacion
 * @property string  $created
 * @property string  $modified
 * @property integer $estado
 *
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProyectosTable extends Table
{
    public function initialize(array $config)
    {
        /**
         * Table's name
         */
        $this->setTable('proyectos');

        /**
         * Behaviors
         */
        $this->addBehavior('Timestamp');

        /**
         * BelongsTo Associations
         */

        // ciudades...
        $this->belongsTo('Ciudades')
            ->setForeignKey('ciudad_id')
            ->setJoinType('INNER')
            ->setConditions(['Ciudades.estado' => 1]);

        // clientes...
        $this->belongsTo('Clientes')
            ->setForeignKey('cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['Clientes.estado' => 1]);

        // marcas...
        $this->belongsTo('Marcas')
            ->setForeignKey('marca_id')
            ->setJoinType('INNER')
            ->setConditions(['Marcas.estado' => 1]);

        // usuarios...
        $this->belongsTo('Usuarios')
            ->setForeignKey('usuario_id')
            ->setJoinType('INNER')
            ->setConditions(['Usuarios.estado' => 1]);

        // periodicidad de pagos...
        $this->belongsTo('PeriodicidadPagos')
            ->setForeignKey('periodicidad_pago_id')
            ->setJoinType('INNER')
            ->setConditions(['PeriodicidadPagos.estado' => 1]);

        // tipos de clientes...
        $this->belongsTo('TipoClientes')
            ->setForeignKey('tipo_cliente_id')
            ->setJoinType('INNER')
            ->setConditions(['TipoClientes.estado' => 1]);

        // tipos de servicios...
        $this->belongsTo('TipoServicios')
            ->setForeignKey('tipo_servicio_id')
            ->setJoinType('INNER')
            ->setConditions(['TipoServicios.estado' => 1]);

        /**
         * HasMany Associations
         */

        // facturaciones...
        $this->hasMany('Facturaciones')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['Facturaciones.estado' => 1]);

        // facturas...
        $this->hasMany('Gastos')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['Gastos.estado' => 1]);

        // horas laboradas...
        $this->hasMany('HoraLaboradas')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['HoraLaboradas.estado' => 1]);

        // proyectos contactos...
        $this->hasMany('ProyectoContactos')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['ProyectoContactos.estado' => 1]);

        // proyectos facturas...
        $this->hasMany('ProyectoFacturas')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['ProyectoFacturas.estado' => 1]);

        // proyecto licencias...
        $this->hasMany('ProyectoLicencias')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['ProyectoLicencias.estado' => 1]);

        // proyectos recursos...
        $this->hasMany('ProyectoRecursos')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['ProyectoRecursos.estado' => 1]);

        // timesheets...
        $this->hasMany('Timesheets')
            ->setForeignKey('proyecto_id')
            ->setJoinType('INNER')
            ->setConditions(['Timesheets.estado' => 1]);

        /**
         * BelongsToMany Associations
         */

        // multimedias...
        $this->belongsToMany('Multimedias', [
            'foreignKey' => 'proyecto_id',
            'joinTable'  => 'proyecto_multimedias',
            'conditions' => ['Multimedias.estado' => 1],
        ]);
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $logProyectos = TableRegistry::get('LogProyectos');

        if ($entity->isNew()) {
            $descripcion = 'Nuevo';
        } else {
            $descripcion = 'Editado';
        }

        /**
         * @see LogProyectos::register
         */
        $logProyectos->register([
            'proyecto_id' => $entity->id,
            'usuario_id'  => $entity->usuario_id,
            'descripcion' => $descripcion,
            'estatus'     => $entity->estatus,
        ]);
    }

    /**
     * Devuelve todos los proyectos activos en listado para dropdown.
     *
     * @param  boolean  $addClave True para devolver clave con nombre
     * @return resource           Query
     */
    public function getAllList($addClave = false)
    {
        return $this->find('list', [
            'keyField'   => 'id',
            'valueField' => function ($row) use ($addClave) {
                return $addClave ? $row['clave'] . ' ' . $row['nombre'] : $row['nombre'];
            },
        ])->where(['estado' => 1]);
    }

    /**
     * Devuelve la clave del proyecto.
     *
     * @param  integer $idCliente Id del cliente
     * @param  integer $idMarca   Id de la marca
     * @return string             Clave del proyecto
     */
    public function getClave($idCliente, $idMarca)
    {
        $clientesTable = TableRegistry::get('Clientes');
        $marcasTable   = TableRegistry::get('Marcas');

        $clienteObj = $clientesTable->find('all')->where(['id' => $idCliente])->first();
        $marcaObj   = $marcasTable->find('all')->where(['id' => $idMarca])->first();

        return substr($clienteObj->nombre, 0, 3) . '-' . substr($marcaObj->nombre, 0, 3);
    }
}
