<?php

namespace App\Controller;

use Cake\I18n\Number;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class GastosController extends AppController
{
    /**
     * Devuelve todos los tipos de gastos.
     *
     * @return array Json response
     */
    public function getTipoGastos() {
        $this->loadModel('TipoGastos');
        $tipoGastos = $this->TipoGastos->findAllByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($tipoGastos)));
    }

    /**
     * Devuelve todos los tipos de pagos.
     *
     * @return array Json response
     */
    public function getTipoPagos()
    {
        $this->loadModel('TipoPagos');
        $tipoPagos = $this->TipoPagos->findAllByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($tipoPagos)));
    }

    /**
     * Devuelve el gasto correspondiente al ID.
     *
     * @param  integer $id Id del gasto
     * @return array       Json response
     */
    public function view($id)
    {
        $multimedias = [];
        $gastoObj    = $this->Gastos->get($id, ['contain' => [
            'Proveedores',
            'Proyectos',
            'TipoGastos',
            'TipoPagos',
            'Usuarios',
            'Multimedias',
        ]]);

        foreach ($gastoObj->multimedias as $multimediaObj) {
            $multimedias[] = [
                'id'             => $multimediaObj->id,
                'version_id'     => $multimediaObj->version_id,
                'nombre_archivo' => $multimediaObj->nombre_archivo,
                'url'            => $multimediaObj->url,
                'tipo_archivo'   => $multimediaObj->tipo_archivo,
            ];
        }

        $this->loadModel('Usuarios');
        $recursoObj = $this->Usuarios->get($gastoObj->recurso_id);
        $data       = [
                'id'            => $gastoObj->id,
                'proyecto_id'   => $gastoObj->proyecto_id,
                'proveedor_id'  => $gastoObj->proveedor_id,
                'recurso_id'    => $gastoObj->recurso_id,
                'tipo_gasto_id' => $gastoObj->tipo_gasto_id,
                'tipo_pago_id'  => $gastoObj->tipo_pago_id,
                'importe'       => $gastoObj->importe,
                'descripcion'   => $gastoObj->descripcion,
                'fecha'         => $gastoObj->fecha->format('Y-m-d'),
                'tipo_pago'     => $gastoObj->tipo_pago->nombre,
                'tipo_gasto'    => $gastoObj->tipo_gasto->nombre,
                'recurso'       => [
                    'id'              => $recursoObj->id,
                    'nombre_completo' => $recursoObj->nombre_completo,
                ],
                'usuario' => [
                    'id'              => $gastoObj->usuario->id,
                    'nombre_completo' => $gastoObj->usuario->nombre_completo,
                ],
                'proyecto' => [
                    'id'     => $gastoObj->proyecto->id,
                    'clave'  => $gastoObj->proyecto->clave,
                    'nombre' => $gastoObj->proyecto->nombre,
                ],
                'proveedor' => [
                    'id'           => $gastoObj->proveedor->id,
                    'nombre'       => $gastoObj->proveedor->nombre,
                    'razon_social' => $gastoObj->proveedor->razon_social,
                    'tipo'         => $gastoObj->proveedor->tipo,
                ],
                'multimedia' => $multimedias,
            ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    /**
     * Devuelve todos los gastos.
     *
     * @return array Json response
     */
    public function getAll()
    {
        $this->loadModel('Usuarios');
        $data   = [];
        $gastos = $this->Gastos
            ->find()
            ->contain([
                'Proveedores',
                'Proyectos',
                'TipoGastos',
                'TipoPagos',
                'Usuarios',
            ])
            ->order('Gastos.id asc')
            ->where(['Gastos.estado' => 1]);

        foreach ($gastos as $gastoObj) {
            $recursoObj = $this->Usuarios->get($gastoObj->recurso_id);
            $data[]     = [
                'id'          => $gastoObj->id,
                'importe'     => Number::currency($gastoObj->importe, 'USD'),
                'descripcion' => $gastoObj->descripcion,
                'fecha'       => $gastoObj->fecha->format('d-m-Y'),
                'tipo_pago'   => $gastoObj->tipo_pago->nombre,
                'tipo_gasto'  => $gastoObj->tipo_gasto->nombre,
                'recurso'     => [
                    'id'              => $recursoObj->id,
                    'nombre_completo' => $recursoObj->nombre_completo,
                ],
                'usuario' => [
                    'id'              => $gastoObj->usuario->id,
                    'nombre_completo' => $gastoObj->usuario->nombre_completo,
                ],
                'proyecto' => [
                    'id'     => $gastoObj->proyecto->id,
                    'clave'  => $gastoObj->proyecto->clave,
                    'nombre' => $gastoObj->proyecto->nombre,
                ],
                'proveedor' => [
                    'id'           => $gastoObj->proveedor->id,
                    'nombre'       => $gastoObj->proveedor->nombre,
                    'razon_social' => $gastoObj->proveedor->razon_social,
                    'tipo'         => $gastoObj->proveedor->tipo,
                ],
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }


    /**
     * Agrega un nuevo gasto
     *
     * @return array Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $message  = 'Ha habido un error al intentar generar el gasto';
            $gastoObj = $this->Gastos->newEntity($this->request->getData());
            $gastoObj->usuario_id = $this->Auth->user('id');

            try {
                $code = $this->Gastos->save($gastoObj) ? 1 : 0;
            } catch (Exception $e) {
            }

            if ($code == 1) {
                $this->loadModel('LogGastos');
                $this->loadModel('GastoMultimedias');

                $idsMultimedia = json_decode($this->request->getData('multimedia_ids'));
                $attributes    = [
                    'gasto_id'    => $gastoObj->id,
                    'usuario_id'  => $this->Auth->user('id'),
                    'descripcion' => 'Nuevo',
                ];

                /**
                 * @see LogGastos::register
                 */
                $this->LogGastos->register($attributes);

                if (!is_null($idsMultimedia) && !empty($idsMultimedia)) {
                    foreach ($idsMultimedia as $idMultimedia) {
                        /**
                         * @see FacturaMultimediasTable::relacionarMultimedia()
                         */
                        $this->GastoMultimedias->relacionarMultimedia($gastoObj->id, $idMultimedia);
                    }
                }
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha generado correctamente el gasto' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Edita el gasto proporcionado.
     *
     * @param  integer $id id del gasto
     * @return array       Json response
     */
    public function edit($id)
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $message  = 'Ha habido un error al intentar editar el gasto';
        $gastoObj = $this->Gastos->get($id);
        $entity   = $this->Gastos->patchEntity($gastoObj, $this->request->getData());

        try {
            $code = $this->Gastos->save($entity) ? 1 : 0;
        } catch (Exception $e) {
        }

        if ($code == 1) {
            $this->loadModel('LogGastos');
            $this->loadModel('GastoMultimedias');

            $idsMultimedia = json_decode($this->request->getData('multimedia_ids'));
            $attributes    = [
                'gasto_id'    => $gastoObj->id,
                'usuario_id'  => $this->Auth->user('id'),
                'descripcion' => 'Editado',
            ];

            /**
             * @see LogGastos::register
             */
            $this->LogGastos->register($attributes);

            if (!is_null($idsMultimedia) && !empty($idsMultimedia)) {
                foreach ($idsMultimedia as $idMultimedia) {
                    /**
                     * @see FacturaMultimediasTable::relacionarMultimedia()
                     */
                    $this->GastoMultimedias->relacionarMultimedia($gastoObj->id, $idMultimedia);
                }
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha editado correctamente el gasto' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Elimina un gasto.
     * Recibe por POST el id del gasto.
     *
     * @return array Json response
     */
    public function delete()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $code     = 0;
        $message  = 'Ha habido un error al intentar eliminar el gasto';
        $gastoObj = $this->Gastos->get($this->request->getData('id'));

        if (empty($gastoObj) || $gastoObj->estado == 0) {
            $code    = 3;
            $message = 'No existe el gasto';
        } else {
            $gastoObj->estado = 0;

            try {
                $code = $this->Gastos->save($gastoObj) ? 1 : $code;
            } catch (Exception $e) {
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha eliminado correctamente el gasto' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }
}