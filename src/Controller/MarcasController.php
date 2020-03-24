<?php

namespace App\Controller;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class MarcasController extends AppController
{
    // public function view($id)
    // {
    //     $clienteObj = $this->Clientes->get($id);

    //     return $this->response
    //         ->withType('application/json')
    //         ->withStringBody(json_encode($clienteObj));
    // }

    public function getAllBYCliente($id)
    {
        $marcas = $this->Marcas->findByClienteIdAndEstado($id, 1)->order('nombre asc')->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($marcas));
    }

    /**
     * Agrega un nuevo cliente
     *
     * @return array Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $params   = $this->request->getData();
            $message  = 'Ya existe una marca con el mismo nombre y cliente';
            $code     = 2;
            $marcaObj = $this->Marcas->find()
                ->where([
                    'cliente_id' => $params['cliente_id'],
                    'nombre'     => $params['nombre'],
                ])
                ->first();

            if (empty($marcaObj)) {
                $marcaObj = $this->Marcas->newEntity();
                $message  = 'Ha habido un error al intentar generar la marca';

                $marcaObj->cliente_id = $params['cliente_id'];
                $marcaObj->nombre     = $params['nombre'];

                try {
                    $code = $this->Marcas->save($marcaObj) ? 1 : 0;
                } catch (Exception $e) {
                }
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha creado correctamente la marca' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Edita un cliente.
     *
     * @param  integer $id Id del cliente
     * @return array       Json response
     */
    // public function edit($id)
    // {
    //     if (!$this->request->is('post')) {
    //         throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
    //     }

    //     $params     = $this->request->getData();
    //     $message    = 'Ha habido un error al intentar editar el cliente';
    //     $clienteObj = $this->Clientes->get($id);

    //     $clienteObj->razon_social = $params['razon_social'];
    //     $clienteObj->nombre       = $params['nombre'];
    //     $clienteObj->rfc          = $params['rfc'];
    //     $clienteObj->direccion    = $params['direccion'];

    //     try {
    //         $code = $this->Clientes->save($clienteObj) ? 1 : 0;
    //     } catch (Exception $e) {
    //     }

    //     $result = [
    //         'code'    => $code,
    //         'message' => $code == 1 ? 'Se ha editado correctamente el cliente' : $message,
    //     ];

    //     return $this->response
    //         ->withType('application/json')
    //         ->withStringBody(json_encode($result));
    // }

    /**
     * Elimina un cliente.
     * Recibe por POST el id del cliente.
     *
     * @return array Json response
     */
    // public function delete()
    // {
    //     if (!$this->request->is('post')) {
    //         throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
    //     }

    //     $code       = 0;
    //     $message    = 'Ha habido un error al intentar eliminar al cliente';
    //     $clienteObj = $this->Clientes->get($this->request->getData('id'));

    //     if (empty($clienteObj) || $clienteObj->estado == 0) {
    //         $code    = 3;
    //         $message = 'No existe el cliente';
    //     } else {
    //         $clienteObj->estado = 0;

    //         try {
    //             $code = $this->Clientes->save($clienteObj) ? 1 : $code;
    //         } catch (Exception $e) {
    //         }
    //     }

    //     $result = [
    //         'code'    => $code,
    //         'message' => $code == 1 ? 'Se ha eliminado correctamente el cliente' : $message,
    //     ];

    //     return $this->response
    //         ->withType('application/json')
    //         ->withStringBody(json_encode($result));
    // }
}
