<?php

namespace App\Controller;

use Cake\I18n\Number;
use App\Model\Entity\Facturacion;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class FacturacionesController extends AppController
{
    public function view($id)
    {
        $facturacionObj = $this->Facturaciones->get($id, ['contain' => [
            'Proyectos',
            'TipoFacturaciones',
            'Usuarios',
            'Multimedias',
        ]]);

        $multimedias     = [];
        $fechaExpedicion = !empty($facturacionObj->fecha_expedicion)
            ? $facturacionObj->fecha_expedicion->format('Y-m-d')
            : '';

        $fechaEstimada = !empty($facturacionObj->fecha_estimada_pago)
            ? $facturacionObj->fecha_estimada_pago->format('Y-m-d')
            : '';

        $fechaPago = !empty($facturacionObj->fecha_pago)
            ? $facturacionObj->fecha_pago->format('Y-m-d')
            : '';

        foreach ($facturacionObj->multimedias as $multimediaObj) {
            $multimedias[] = [
                'id'             => $multimediaObj->id,
                'version_id'     => $multimediaObj->version_id,
                'nombre_archivo' => $multimediaObj->nombre_archivo,
                'url'            => $multimediaObj->url,
                'tipo_archivo'   => $multimediaObj->tipo_archivo,
            ];
        }

        $data = [
            'id'                      => $facturacionObj->id,
            'proyecto_id'             => $facturacionObj->proyecto_id,
            'tipo_facturacion_id'     => $facturacionObj->tipo_facturacion_id,
            'usuario_id'              => $facturacionObj->usuario_id,
            'numero_factura'          => $facturacionObj->numero_factura,
            'numero_proyecto_cliente' => $facturacionObj->numero_proyecto_cliente,
            'descripcion'             => $facturacionObj->descripcion,
            'orden_compra'            => $facturacionObj->orden_compra,
            'moneda'                  => $facturacionObj->moneda,
            'importe'                 => $facturacionObj->importe,
            'iva'                     => $facturacionObj->iva,
            'total'                   => $facturacionObj->total,
            'fecha_expedicion'        => $fechaExpedicion,
            'fecha_estimada_pago'     => $fechaEstimada,
            'fecha_pago'              => $fechaPago,
            'estatus'                 => $facturacionObj->estatus,
            'proyecto'                => [
                'id'     => $facturacionObj->proyecto->id,
                'nombre' => $facturacionObj->proyecto->nombre,
                'clave'  => $facturacionObj->proyecto->clave,
            ],
            'usuario'                 => [
                'nombre_completo' => $facturacionObj->usuario->nombre_completo,
                'email'           => $facturacionObj->usuario->email,
            ],
            'tipo_facturacion'        => [
                'id'     => $facturacionObj->tipo_facturacion->id,
                'clave'  => $facturacionObj->tipo_facturacion->clave,
                'nombre' => $facturacionObj->tipo_facturacion->nombre,
            ],
            'multimedia' => $multimedias,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    public function getAll()
    {
        $data          = [];
        $facturaciones = $this->Facturaciones
            ->find()
            ->contain([
                'Proyectos',
                'TipoFacturaciones',
                'Usuarios',
            ])
            ->order('Facturaciones.id asc')
            ->where(['Facturaciones.estado' => 1]);

        foreach ($facturaciones as $facturacionObj) {
            $fechaExpedicion = !empty($facturacionObj->fecha_expedicion)
                ? $facturacionObj->fecha_expedicion->format('d-m-Y')
                : '';

            $fechaEstimada = !empty($facturacionObj->fecha_estimada_pago)
                ? $facturacionObj->fecha_estimada_pago->format('d-m-Y')
                : '';

            $fechaPago = !empty($facturacionObj->fecha_pago)
                ? $facturacionObj->fecha_pago->format('d-m-Y')
                : '';

            $data[] = [
                'id'                      => $facturacionObj->id,
                'numero_factura'          => $facturacionObj->numero_factura,
                'numero_proyecto_cliente' => $facturacionObj->numero_proyecto_cliente,
                'descripcion'             => $facturacionObj->descripcion,
                'orden_compra'            => $facturacionObj->orden_compra,
                'moneda'                  => $facturacionObj->moneda,
                'importe'                 => Number::currency($facturacionObj->importe, 'USD'),
                'iva'                     => Number::currency($facturacionObj->iva, 'USD'),
                'total'                   => Number::currency($facturacionObj->total,  'USD'),
                'fecha_expedicion'        => $fechaExpedicion,
                'fecha_estimada_pago'     => $fechaEstimada,
                'fecha_pago'              => $fechaPago,
                'estatus'                 => $facturacionObj->estatus,
                'usuario'                 => [
                    'id'              => $facturacionObj->usuario->id,
                    'nombre_completo' => $facturacionObj->usuario->nombre_completo,
                ],
                'proyecto' => [
                    'id'     => $facturacionObj->proyecto->id,
                    'clave'  => $facturacionObj->proyecto->clave,
                    'nombre' => $facturacionObj->proyecto->nombre,
                ],
                'tipo_facturacion' => [
                    'id'     => $facturacionObj->tipo_facturacion->id,
                    'clave'  => $facturacionObj->tipo_facturacion->clave,
                    'nombre' => $facturacionObj->tipo_facturacion->nombre,
                ],
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    public function getTipoFacturaciones()
    {
        $this->loadModel('TipoFacturaciones');
        $tipoFacturaciones = $this->TipoFacturaciones->findAllByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($tipoFacturaciones)));
    }

    /**
     * Agrega una nueva facturación.
     *
     * @return array Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $message        = 'Ha habido un error al intentar generar la facturación';
            $facturacionObj = $this->Facturaciones->newEntity($this->request->getData());

            $facturacionObj->usuario_id = $this->Auth->user('id');

            if (!empty($this->request->getData('fecha_pago'))) {
                $facturacionObj->estatus = Facturacion::ESTATUS_PAGADO;
            } else {
                $facturacionObj->estatus = Facturacion::ESTATUS_SOLICITADA;
            }

            try {
                $code = $this->Facturaciones->save($facturacionObj) ? 1 : 0;
            } catch (Exception $e) {
            }

            if ($code == 1) {
                $this->loadModel('FacturacionMultimedias');
                $this->loadModel('Proyectos');
                $this->loadModel('LogFacturaciones');

                $idsMultimedia = json_decode($this->request->getData('multimedia_ids'));
                $attributes    = [
                    'facturacion_id' => $facturacionObj->id,
                    'usuario_id'     => $this->Auth->user('id'),
                    'descripcion'    => 'Nuevo',
                ];

                /**
                 * @see LogFacturaciones::register()
                 */
                $this->LogFacturaciones->register($attributes);

                if (!is_null($idsMultimedia) && !empty($idsMultimedia)) {
                    foreach ($idsMultimedia as $idMultimedia) {
                        /**
                         * @see FacturaMultimediasTable::relacionarMultimedia()
                         */
                        $this->FacturacionMultimedias->relacionarMultimedia($facturacionObj->id, $idMultimedia);
                    }
                }

                // enviamos email...
                $proyectoObj = $this->Proyectos->get($this->request->getData('proyecto_id'));
                $usuario     = $this->Auth->user('nombre') . ' ' . $this->Auth->user('apellido_paterno')
                    . ' ' . $this->Auth->user('apellido_materno');

                $emailObj = new \Cake\Mailer\Email();
                $emailObj->viewBuilder()->setTemplate('solicitud_facturacion');
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
                        'armando.romero@edge.com.mx' => 'Armando Romero Mayen',
                        'eromero@ifahtodigital.com'  => 'Esteban Romero'
                    ])
                    ->setSubject('Nueva solicitud de facturación | Ifahto Digital')
                    ->send();
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha generado correctamente la facturación' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    public function edit($id)
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $message        = 'Ha habido un error al intentar editar la facturación';
        $facturacionObj = $this->Facturaciones->get($id, ['contain' => ['Multimedias']]);
        $entity         = $this->Facturaciones->patchEntity($facturacionObj, $this->request->getData());

        try {
            $code = $this->Facturaciones->save($entity) ? 1 : 0;
        } catch (Exception $e) {
        }

        if ($code == 1) {
            $this->loadModel('LogFacturaciones');
            $this->loadModel('FacturacionMultimedias');

            $idsMultimedia = json_decode($this->request->getData('multimedia_ids'));
            $attributes    = [
                'facturacion_id' => $facturacionObj->id,
                'usuario_id'     => $this->Auth->user('id'),
                'descripcion'    => 'Nuevo',
            ];

            /**
             * @see LogFacturaciones::register()
             */
            $this->LogFacturaciones->register($attributes);

            if (!is_null($idsMultimedia) && !empty($idsMultimedia)) {
                foreach ($idsMultimedia as $idMultimedia) {
                    /**
                     * @see FacturaMultimediasTable::relacionarMultimedia()
                     */
                    $this->FacturacionMultimedias->relacionarMultimedia($facturacionObj->id, $idMultimedia);
                }
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha editado correctamente la facturación' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Elimina una facturación.
     * Recibe por POST el id de la facturación.
     *
     * @return array Json response
     */
    public function delete()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $code           = 0;
        $message        = 'Ha habido un error al intentar eliminar la facturación';
        $facturacionObj = $this->Facturaciones->get($this->request->getData('id'));

        if (empty($facturacionObj) || $facturacionObj->estado == 0) {
            $code    = 3;
            $message = 'No existe la facturación';
        } else {
            $facturacionObj->estado = 0;

            try {
                $code = $this->Facturaciones->save($facturacionObj) ? 1 : $code;
            } catch (Exception $e) {
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha eliminado correctamente la facturación' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Cambia el estatus de la facturación.
     *
     * @param  integer $id Id de la facturación
     * @return array       Json response
     */
    public function cambiarEstatus($id)
    {
        if (is_null($id)) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $message                 = 'Ha habido un error al intentar cambiar el estatus';
        $facturacionObj          = $this->Facturaciones->get($id);
        $facturacionObj->estatus = $this->request->getData('estatus');

        try {
            $code = $this->Facturaciones->save($facturacionObj) ? 1 : 0;
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
