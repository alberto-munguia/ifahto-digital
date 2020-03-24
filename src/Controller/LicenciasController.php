<?php

namespace App\Controller;

use Cake\I18n\Number;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class LicenciasController extends AppController
{
    /**
     * Devuelve todas las licencias.
     *
     * @return array Json response
     */
    public function getAll()
    {
        $data      = [];
        $licencias = $this->Licencias
            ->find()
            ->contain([
                'Proveedores',
                'TipoPagos',
                'Usuarios',
            ])
            ->where(['Licencias.estado' => 1])
            ->group('Licencias.id');

        foreach ($licencias as $licenciaObj) {
            $data[] = [
                'id'           => $licenciaObj->id,
                'proveedor_id' => $licenciaObj->proveedor_id,
                'tipo_pago_id' => $licenciaObj->tipo_pago_id,
                'usuario_id'   => $licenciaObj->usuario_id,
                'nombre'       => $licenciaObj->nombre,
                'descripcion'  => $licenciaObj->descripcion,
                'importe'      => Number::currency($licenciaObj->importe),
                'tipo_pago'    => !empty($licenciaObj->tipo_pago) ? $licenciaObj->tipo_pago->nombre : '',
                'fecha'        => !empty($licenciaObj->fecha) ? $licenciaObj->fecha->format('d-m-Y') : '',
                'usuario'      => [
                    'id'              => !empty($licenciaObj->usuario) ? $licenciaObj->usuario->id : '',
                    'nombre_completo' => !empty($licenciaObj->usuario) ? $licenciaObj->usuario->nombre_completo : '',
                ],
                'proveedor'   => [
                    'id'           => $licenciaObj->proveedor->id,
                    'nombre'       => $licenciaObj->proveedor->nombre,
                    'razon_social' => $licenciaObj->proveedor->razon_social,
                    'tipo'         => $licenciaObj->proveedor->tipo,
                ],
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    public function view($id)
    {
        $licenciaObj = $this->Licencias->get($id, ['contain' => [
            'Proveedores',
            'TipoPagos',
            'Usuarios',
            'Multimedias',
        ]]);

        $multimedias = [];

        foreach ($licenciaObj->multimedias as $multimediaObj) {
            $multimedias[] = [
                'id'             => $multimediaObj->id,
                'nombre_archivo' => $multimediaObj->nombre_archivo,
                'url'            => $multimediaObj->url,
                'tipo_archivo'   => $multimediaObj->tipo_archivo,
            ];
        }

        $data = [
            'id'           => $licenciaObj->id,
            'proveedor_id' => $licenciaObj->proveedor_id,
            'tipo_pago_id' => $licenciaObj->tipo_pago_id,
            'usuario_id'   => $licenciaObj->usuario_id,
            'nombre'       => $licenciaObj->nombre,
            'descripcion'  => $licenciaObj->descripcion,
            'importe'      => $licenciaObj->importe,
            'tipo_pago'    => !empty($licenciaObj->tipo_pago) ? $licenciaObj->tipo_pago->nombre : '',
            'fecha'        => !empty($licenciaObj->fecha) ? $licenciaObj->fecha->format('d-m-Y') : '',
            'usuario'      => [
                'id'              => !empty($licenciaObj->usuario) ? $licenciaObj->usuario->id : '',
                'nombre_completo' => !empty($licenciaObj->usuario) ? $licenciaObj->usuario->nombre_completo : '',
            ],
            'proveedor'   => [
                'id'           => $licenciaObj->proveedor->id,
                'nombre'       => $licenciaObj->proveedor->nombre,
                'razon_social' => $licenciaObj->proveedor->razon_social,
                'tipo'         => $licenciaObj->proveedor->tipo,
            ],
            'multimedia' => $multimedias,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    /**
     * Agrega una nueva licencia
     *
     * @return array Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $message     = 'Ha habido un error al intentar generar la licencia';
            $licenciaObj = $this->Licencias->newEntity($this->request->getData());

            try {
                $code = $this->Licencias->save($licenciaObj) ? 1 : 0;
            } catch (Exception $e) {
            }

            if ($code == 1) {
                $this->loadModel('LicenciaMultimedias');

                $idsMultimedia = json_decode($this->request->getData('multimedia_ids'));

                if (!is_null($idsMultimedia) && !empty($idsMultimedia)) {
                    foreach ($idsMultimedia as $idMultimedia) {
                        /**
                         * @see LicenciaMultimediasTable::relacionarMultimedia()
                         */
                        $this->LicenciaMultimedias->relacionarMultimedia($licenciaObj->id, $idMultimedia);
                    }
                }
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha generado correctamente la licencia' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Elimina una licencia.
     * Recibe por POST el id de la licencia.
     *
     * @return array Json response
     */
    public function delete()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $code        = 0;
        $message     = 'Ha habido un error al intentar eliminar la licencia';
        $licenciaObj = $this->Licencias->get($this->request->getData('id'));

        if (empty($licenciaObj) || $licenciaObj->estado == 0) {
            $code    = 3;
            $message = 'No existe la licencia';
        } else {
            $licenciaObj->estado = 0;

            try {
                $code = $this->Licencias->save($licenciaObj) ? 1 : $code;
            } catch (Exception $e) {
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha eliminado correctamente la licencia' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Asocia el(los) proyecto(s) a la licencia proporcionada.
     *
     * @param  integer $id Id de la licencia
     * @return array       Json response
     */
    public function relacionarProyectos($id = null)
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $this->loadModel('Proyectos');
        $this->loadModel('ProyectoLicencias');

        $licenciaObj = $this->Licencias->get($id);
        $code        = 0;
        $message     = 'Ha habido un error al intentar relacionar el proyecto con la licencia';
        $ids         = json_decode($this->request->getData('proyecto_ids'));

        foreach ($ids as $idProyecto) {
            $proyectoLicenciaObj = $this->ProyectoLicencias->findByProyectoIdAndLicenciaId(
                $idProyecto,
                $licenciaObj->id
            )->first();

            if (!empty($proyectoLicenciaObj)) {
                if ($proyectoLicenciaObj->estado == 0) {
                    $proyectoLicenciaObj->estado = 1;

                    try {
                        $code = $this->ProyectoLicencias->save($proyectoLicenciaObj);
                    } catch (Exception $e) {
                    }
                }
            } else {
                $proyectoLicenciaObj = $this->ProyectoLicencias->newEntity();
                $proyectoLicenciaObj->proyecto_id = $idProyecto;
                $proyectoLicenciaObj->licencia_id = $licenciaObj->id;

                try {
                    $code = $this->ProyectoLicencias->save($proyectoLicenciaObj) ? 1 : 0;
                } catch (Exception $e) {
                }
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha relacionado correctamente' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }
}