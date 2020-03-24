<?php

namespace App\Controller;

use App\Model\Entity\Multimedia;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class MultimediasController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Multimedia');
    }
    /**
     * Sube un archivo al sistema.
     *
     * @return array Json response
     */
    public function upload()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\NotFoundException('Página no encontrada.');
        }

        $params = $this->request->getData();

        /**
         * @see MultimediaComponent::uploadFile
         */
        $multimediaObj = $this->Multimedia->uploadFile($params['tipo'], $params['file']);
        $code          = 0;
        $idMultimedia  = 0;

        if ($multimediaObj instanceof \App\Model\Entity\Multimedia) {
            $code         = 1;
            $idMultimedia = $multimediaObj->id;
        }

        $result = [
            'code'         => $code,
            'idMultimedia' => $idMultimedia,
            'file'         => $params['file']['name'],
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Elimina un archivo.
     *
     * @return array Json response
     */
    public function delete()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\NotFoundException('Página no encontrada.');
        }

        $code          = 0;
        $message       = 'Ha habido un error al intentar eliminar el archivo';
        $multimediaObj = $this->Multimedias->get($this->request->getData('id'), ['contain' => ['Proyectos']]);

        if (!empty($multimediaObj)) {
            $multimediaObj->estado = 0;

            try {
                $code = $this->Multimedias->save($multimediaObj) ? 1 : 0;
            } catch (Exception $e) {
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha eliminado correctamente el archivo' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Devuelve todas las facturas existentes.
     *
     * @return array Json response
     */
    public function getFacturas()
    {
        if (!$this->request->is('get')) {
            throw new \Cake\Http\Exception\NotFoundException('Página no encontrada.');
        }

        $id          = $this->request->getQuery('id');
        $multimedias = [];
        $data        = [];

        switch ($this->request->getQuery('tipo')) {
            case 'gastos':
                $this->loadModel('Gastos');
                $gasto = $this->Gastos->get($id, ['contain' => ['Multimedias']]);

                if (!empty($gasto)) {
                    $multimedias = $gasto->multimedias;
                }
                break;

            case 'factura':
                $this->loadModel('Facturaciones');
                $facturacion = $this->Facturaciones->get($id, ['contain' => ['Multimedias']]);

                if (!empty($facturacion)) {
                    $multimedias = $facturacion->multimedias;
                }
                break;

            case 'licencia':
                $this->loadModel('Licencias');
                $licencia = $this->Licencias->get($id, ['contain' => ['Multimedias']]);

                if (!empty($licencia)) {
                    $multimedias = $licencia->multimedias;
                }
                break;
        }

        foreach ($multimedias as $multimediaObj) {
            if (empty($multimediaObj) || $multimediaObj->estado == 0) {
                continue;
            }

            $data[] = [
                'nombre_archivo' => $multimediaObj->nombre_archivo,
                'url'            => $multimediaObj->url,
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['multimedias' => $data]));
    }
}
