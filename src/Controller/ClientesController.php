<?php

namespace App\Controller;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ClientesController extends AppController
{
    public function view($id)
    {
        $clienteObj = $this->Clientes->get($id);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($clienteObj));
    }

    public function getAll()
    {
        $page    = $this->request->getQuery('page');
        $perPage = $this->request->getQuery('per_page');
        $sort    = $this->request->getQuery('sort');

        $query = $this->Clientes->find()->where(['estado' => 1]);

        if (!is_null($perPage)) {
            $query->limit($perPage);
        }

        if (!is_null($page)) {
            $query->page($page);
        }

        if (!is_null($sort) && !empty($sort)) {
            $sortingBy = explode('|', $sort);
            $query->order([$sortingBy[0] => $sortingBy[1]]);
        }

        $query->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($query));
    }

    public function getByProyecto($idProyecto = null)
    {
        if (is_null($idProyecto) || empty($idProyecto)) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $clienteObj = $this->Clientes->find()
            ->innerJoinWith('Proyectos', function ($q) use ($idProyecto) {
                return $q->where(['Proyectos.id' => $idProyecto]);
            })
            ->where(['Clientes.estado' => 1])
            ->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($clienteObj));
    }

    public function getTipoClientes()
    {
        $this->loadModel('TipoClientes');
        $tipoClientes = $this->TipoClientes->findByEstado(1)->order('nombre asc')->toArray();

        return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($tipoClientes));
    }

    /**
     * Agrega un nuevo cliente
     *
     * @return array Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $clienteObj = $this->Clientes->findByRazonSocial($this->request->getData('razon_social'))->first();
            $code       = 2;
            $message    = 'Ya existe un cliente con la misma razón social';

            if (empty($clienteObj)) {
                $clienteObj = $this->Clientes->newEntity($this->request->getData());
                $message    = 'Ha habido un error al intentar generar el cliente';

                try {
                    $code = $this->Clientes->save($clienteObj) ? 1 : 0;
                } catch (Exception $e) {
                }
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha creado correctamente el cliente' : $message,
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
    public function edit($id)
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $params     = $this->request->getData();
        $message    = 'Ha habido un error al intentar editar el cliente';
        $clienteObj = $this->Clientes->get($id);

        $clienteObj->razon_social = $params['razon_social'];
        $clienteObj->nombre       = $params['nombre'];
        $clienteObj->rfc          = $params['rfc'];
        $clienteObj->direccion    = $params['direccion'];

        try {
            $code = $this->Clientes->save($clienteObj) ? 1 : 0;
        } catch (Exception $e) {
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha editado correctamente el cliente' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Elimina un cliente.
     * Recibe por POST el id del cliente.
     *
     * @return array Json response
     */
    public function delete()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $code       = 0;
        $message    = 'Ha habido un error al intentar eliminar al cliente';
        $clienteObj = $this->Clientes->get($this->request->getData('id'));

        if (empty($clienteObj) || $clienteObj->estado == 0) {
            $code    = 3;
            $message = 'No existe el cliente';
        } else {
            $clienteObj->estado = 0;

            try {
                $code = $this->Clientes->save($clienteObj) ? 1 : $code;
            } catch (Exception $e) {
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha eliminado correctamente el cliente' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }
}
