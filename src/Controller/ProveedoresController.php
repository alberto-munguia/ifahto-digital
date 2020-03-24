<?php

namespace App\Controller;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ProveedoresController extends AppController
{
    public function view($id)
    {
        $proveedorObj = $this->Proveedores->get($id);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($proveedorObj));
    }

    public function getAll()
    {
        $proveedores = $this->Proveedores->findAllByEstado(1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($proveedores)));
    }

    /**
     * Agrega un nuevo proveedor
     *
     * @return array Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $code         = 0;
            $message      = 'Ha habido un error al intentar generar el proveedor';
            $proveedorObj = $this->Proveedores->findByRazonSocial($this->request->getData('razon_social'))->first();

            if (!empty($proveedorObj)) {
                if ($proveedorObj->estado == 0) {
                    $entity = $this->Proveedores->pathEntity($proveedorObj, $this->request->getData());
                    $entity->estado = 1;

                    try {
                        $code = $this->Proveedores->save($entity) ? 1 : 0;
                    } catch (Exception $e) {
                    }
                } else {
                    $code    = 2;
                    $message = 'Ya existe un proveedor con la misma razón social';
                }
            } else {
                $proveedorObj = $this->Proveedores->newEntity($this->request->getData());

                try {
                    $code = $this->Proveedores->save($proveedorObj) ? 1 : 0;
                } catch (Exception $e) {
                }
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha creado correctamente el proveedor' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Edita un proveedor
     *
     * @return array Json response
     */
    public function edit($id)
    {
        if ($this->request->is('post')) {
            $params       = $this->request->getData();
            $proveedorObj = $this->Proveedores->get($id);
            $message      = 'Ha habido un error al intentar editar el proveedor';

            $proveedorObj->razon_social = $params['razon_social'];
            $proveedorObj->nombre       = $params['nombre'];
            $proveedorObj->tipo         = $params['tipo'];

            try {
                $code = $this->Proveedores->save($proveedorObj) ? 1 : 0;
            } catch (Exception $e) {
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha editado correctamente el proveedor' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Elimina un proveedor.
     * Recibe por POST el id del proveedor.
     *
     * @return array Json response
     */
    public function delete()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $code         = 0;
        $message      = 'Ha habido un error al intentar eliminar al proveedor';
        $proveedorObj = $this->Proveedores->get($this->request->getData('id'));

        if (empty($proveedorObj) || $proveedorObj->estado == 0) {
            $code    = 3;
            $message = 'No existe el proveedor';
        } else {
            $proveedorObj->estado = 0;

            try {
                $code = $this->Proveedores->save($proveedorObj) ? 1 : $code;
            } catch (Exception $e) {
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha eliminado correctamente el proveedor' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }
}
