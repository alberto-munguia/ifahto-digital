<?php

namespace App\Controller;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class ContactosController extends AppController
{
    /**
     * Devuelve todos los contactos.
     *
     * @return array Json response
     */
    public function getAll()
    {
        $contactos = $this->Contactos->findByEstado(1)->order('nombre asc');
        $data      = [];

        foreach ($contactos as $contactoObj) {
            $data[] = [
                'id'              => $contactoObj->id,
                'nombre_completo' => $contactoObj->nombre_completo,
                'email'           => $contactoObj->email,
                'telefono'        => $contactoObj->telefono,
                'extension'       => $contactoObj->extension,
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    /**
     * Genera un nuevo contacto.
     *
     * @return array Json response
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $params      = $this->request->getData();
            $contactoObj = $this->Contactos->find('byEmail', ['email' => $params['email']]);
            $code        = 2;
            $message     = 'Ya existe un cliente con el mismo correo electrónico';

            if (empty($contactoObj)) {
                $contactoObj = $this->Contactos->newEntity();
                $message     = 'Ha habido un error al intentar generar el cliente';

                $contactoObj->nombre           = $params['nombre'];
                $contactoObj->apellido_paterno = $params['apellido_paterno'];
                $contactoObj->apellido_materno = !empty($params['apellido_materno']) ? $params['apellido_materno'] : '';
                $contactoObj->email            = $params['email'];
                $contactoObj->telefono         = !empty($params['telefono']) ? $params['telefono'] : '';
                $contactoObj->extension        = !empty($params['extension']) ? $params['extension'] : '';

                try {
                    $code = $this->Contactos->save($contactoObj) ? 1 : 0;
                } catch (Exception $e) {
                }
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha creado correctamente el contacto' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }
}
