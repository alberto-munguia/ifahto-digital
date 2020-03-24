<?php

namespace App\Controller;

use Cake\I18n\FrozenTime;
use Cake\I18n\Date;
use App\Model\Entity\Proyecto;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProyectosController extends AppController
{
    /**
     * Devuelve todos los proyectos.
     *
     * @return array Json response
     */
    public function getAll()
    {
        $conditions = ['Proyectos.estado' => 1];

        if (!empty($this->request->getQuery('query'))) {
            $query = json_decode($this->request->getQuery('query'));

            foreach ($query as $key => $value) {
                $conditions[$key] = $value;
            }
        }

        $proyectos = $this->Proyectos
            ->find()
            ->contain(['Clientes', 'Marcas', 'Usuarios'])
            ->where($conditions)
            ->order('Proyectos.nombre asc')
            ->toArray();

        foreach ($proyectos as &$proyecto) {
            $date           = new Date($proyecto['created']);
            $nombreCompleto = $proyecto['usuario']['nombre'] . ' ' .
                $proyecto['usuario']['apellido_paterno'] . ' ' . $proyecto['usuario']['apellido_materno'];

            $proyecto['usuario']['nombre_completo'] = trim($nombreCompleto);
            $proyecto['fecha_registro']             = $date->format('d-m-Y');
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($proyectos)));
    }

    /**
     * Devuelve el proyecto.
     *
     * @param  integer $id Id del proyecto
     * @return array       Json response
     */
    public function getById($id = null)
    {
        if (is_null($id) || empty($id)) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $proyectoObj = $this->Proyectos->get($id, ['contain' => [
            'Clientes',
        ]]);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($proyectoObj)));
    }

    public function getPeriodicidadesPago()
    {
        $this->loadModel('PeriodicidadPagos');
        $periodicidades = $this->PeriodicidadPagos->findByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($periodicidades)));
    }

    public function getTipoServicios()
    {
        $this->loadModel('TipoServicios');
        $tipoServicios = $this->TipoServicios->findByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($tipoServicios)));
    }

    public function view($id = null)
    {
        if (is_null($id)) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $proyectoObj = $this->Proyectos->get($id, [
            'contain' => [
                'Clientes',
                'Marcas',
                'TipoClientes',
                'TipoServicios',
                'Ciudades',
                'Ciudades.EntidadFederativas',
                'Multimedias',
                'ProyectoContactos',
                'ProyectoContactos.Contactos',
                'ProyectoRecursos',
                'ProyectoRecursos.Usuarios',
                'PeriodicidadPagos',
            ],
        ]);

        $this->set([
            'proyecto' => $proyectoObj,
        ]);
    }

    /**
     * Genera un nuevo proyecto.
     *
     * @return json Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $this->loadModel('Usuarios');

            $message           = 'Ha habido un error al intentar crear el proyecto';
            $proyectoObj       = $this->Proyectos->newEntity($this->request->getData());
            $fechaEntrega      = '';
            $fechaFinalizacion = '';
            $idCliente         = $this->request->getData('cliente_id');
            $idMarca           = $this->request->getData('marca_id');
            $clave             = $this->Proyectos->getClave($idCliente, $idMarca);

            if (!empty($this->request->getData('fecha_entrega'))) {
                $fechaEntrega = $this->request->getData('fecha_entrega');

                if (!empty($this->request->getData('hora_entrega'))) {
                    $fechaEntrega .= ' ' . $this->request->getData('hora_entrega');
                }
            }

            if (!empty($this->request->getData('fecha_finalizacion'))) {
                $fechaFinalizacion = $this->request->getData('fecha_finalizacion');

                if (!empty($this->request->getData('hora_finalizacion'))) {
                    $fechaFinalizacion .= ' ' . $this->request->getData('hora_finalizacion');
                }
            }

            $usuarioResponsableObj = $this->Usuarios->get($this->request->getData('responsable_id'));

            $proyectoObj->usuario_id   = $this->Auth->user('id');
            $proyectoObj->responsable  = $usuarioResponsableObj->nombre_completo;
            $proyectoObj->estatus      = Proyecto::ESTATUS_DESARROLLO;
            $proyectoObj->clave        = strtoupper($clave);
            $proyectoObj->entrega      = $fechaEntrega;
            $proyectoObj->finalizacion = $fechaFinalizacion;
            $proyectoObj->anticipo     = !empty($this->request->getData('anticipo'))
                ? $this->request->getData('anticipo')
                : '';

            $proyectoObj->porcentaje_anticipo = !empty($this->request->getData('porcentaje_anticipo'))
                ? (int) $this->request->getData('porcentaje_anticipo')
                : 0;

            try {
                $code = $this->Proyectos->save($proyectoObj) ? 1 : 0;
            } catch (Exception $e) {
            }

            if ($code == 1) {
                $this->loadModel('ProyectoContactos');
                $this->loadModel('ProyectoMultimedias');
                $this->loadModel('ProyectoRecursos');

                for ($i = 1; $i <= 2; $i++) {
                    if ($i == 1) {
                        $idContacto = $this->request->getData('contacto_responsable_id');
                        $tipo       = \App\Model\Entity\ProyectoContacto::TIPO_RESPONSABLE;
                    } else {
                        $idContacto = $this->request->getData('contacto_facturacion_id');
                        $tipo       = \App\Model\Entity\ProyectoContacto::TIPO_FACTURACION;
                    }

                    /**
                     * @see ProyectoContactosTable::relacionarContacto()
                     */
                    $proyectoContactoObj = $this->ProyectoContactos->relacionarContacto(
                        $proyectoObj->id,
                        $idContacto,
                        $tipo
                    );
                }

                $idsMultimedia = $this->request->getData('multimedia_ids');

                if (!is_null($idsMultimedia) && !empty($idsMultimedia)) {
                    $ids = explode('|', $idsMultimedia);

                    foreach ($ids as $idMultimedia) {
                        /**
                         * @see ProyectoMultimediasTable::relacionarMultimedia()
                         */
                        $this->ProyectoMultimedias->relacionarMultimedia($proyectoObj->id, $idMultimedia);
                    }
                }

                $recursos = json_decode($this->request->getData('usuarios_relacionados'));

                if (!is_null($recursos) && !empty($recursos)) {
                    foreach ($recursos as $recurso) {
                        /**
                         * @see ProyectoRecursosTable::relacionarUsuario()
                         */
                        $this->ProyectoRecursos->relacionarUsuario(
                            $proyectoObj->id,
                            $recurso->usuario_id,
                            $recurso->total_hora
                        );
                    }
                }

                $usuario  = $this->Auth->user('nombre') . ' ' . $this->Auth->user('apellido_paterno') . ' '
                    . $this->Auth->user('apellido_materno');

                $emailObj = new \Cake\Mailer\Email();
                $emailObj->viewBuilder()->setTemplate('nuevo_proyecto');
                $emailObj
                    ->setEmailFormat('html')
                    ->setViewVars([
                        'usuario' => trim($usuario),
                        'clave'   => $proyectoObj->clave,
                        'nombre'  => $proyectoObj->nombre,
                    ])
                    ->setFrom(['intranet@edg3web.com' => 'Intranet Ifahto Digital'])
                    ->setTo(['victoria.erana@edge.com.mx' => 'Victoria Alicia Eraña Olguin'])
                    ->addTo([
                        'garaiza@ifahtodigital.com'  => 'Gabriel Araiza',
                        'armando.romero@edge.com.mx' => 'Armando Romero Mayen',
                        'eromero@ifahtodigital.com'  => 'Esteban Romero',
                    ])
                    ->setSubject('Se ha creado un nuevo proyecto | Ifahto Digital')
                    ->send();
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha creado correctamente el proyecto' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Edita un proyecto.
     *
     * @param  integer $id Id del proyecto
     * @return array       Json response
     */
    public function edit($id = null)
    {
        if (is_null($id)) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $this->render('add');
    }

    /**
     * Cambia el estatus del proyecto.
     *
     * @param  integer $id Id del proyecto
     * @return array       Json response
     */
    public function cambiarEstatus($id)
    {
        if (is_null($id)) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $message     = 'Ha habido un error al intentar cambiar el estatus';
        $proyectoObj = $this->Proyectos->get($id);
        $proyectoObj->estatus = $this->request->getData('estatus');

        try {
            $code = $this->Proyectos->save($proyectoObj) ? 1 : 0;
        } catch (Exception $e) {
        }

        $result = [
            'code' => $code,
            'message' => $code == 1 ? 'Se ha editado correctamente el estatus' : $message,
        ];

        return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
    }
}
