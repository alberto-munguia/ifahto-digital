<?php

namespace App\Controller;

use Cake\I18n\Time;
use Cake\I18n\Date;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class UsuariosController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('String');
    }

    /**
     * Devuelve un usuario.
     *
     * @param  integer $id Id del usuario
     * @return array       Json response
     */
    public function view($id = null)
    {
        $usuarioObj = $this->Usuarios->get($id, ['contain' => ['Puestos', 'Perfiles']]);
        $data       = [
            'id'               => $usuarioObj->id,
            'puesto_id'        => $usuarioObj->puesto_id,
            'perfil_id'        => $usuarioObj->perfil_id,
            'nombre'           => $usuarioObj->nombre,
            'apellido_paterno' => $usuarioObj->apellido_paterno,
            'apellido_materno' => $usuarioObj->apellido_materno,
            'nombre_completo'  => $usuarioObj->nombre_completo,
            'email'            => $usuarioObj->email,
            'puesto'           => $usuarioObj->puesto->nombre,
            'perfil'           => $usuarioObj->perfil->nombre,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    /**
     * Devuelve todos los usuarios.
     *
     * @return array Json response
     */
    public function getAll()
    {
        $data     = [];
        $usuarios = $this->Usuarios
            ->find()
            ->contain(['Puestos',  'Perfiles'])
            ->where(['Usuarios.estado' => 1])
            ->order('Usuarios.nombre')
            ->all();

        foreach ($usuarios as $usuarioObj) {
            $fechaInicio = !empty($usuarioObj->fecha_inicio)
                ? $usuarioObj->fecha_inicio->format('d-m-Y')
                : '';

            $data[] = [
                'id'               => $usuarioObj->id,
                'puesto_id'        => $usuarioObj->puesto_id,
                'perfil_id'        => $usuarioObj->perfil_id,
                'nombre'           => $usuarioObj->nombre,
                'apellido_paterno' => $usuarioObj->apellido_paterno,
                'apellido_materno' => $usuarioObj->apellido_materno,
                'nombre_completo'  => $usuarioObj->nombre_completo,
                'email'            => $usuarioObj->email,
                'fecha_inicio'     => $fechaInicio,
                'puesto'           => $usuarioObj->puesto->nombre,
                'perfil'           => $usuarioObj->perfil->nombre,
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(($data)));
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $usuario = $this->Auth->identify();
            $code    = 0;

            if (!empty($usuario)) {
                $this->loadModel('Perfiles');
                $this->loadModel('Puestos');

                $perfil = $this->Perfiles->get($usuario['perfil_id'])->toArray();
                $puesto = $this->Puestos->get($usuario['puesto_id'])->toArray();
                $code   = 1;

                $usuario['perfil'] = $perfil;
                $usuario['puesto'] = $puesto;
                $this->Auth->setUser($usuario);
            }

            $result = [
                'code'    => $code,
                'message' => $code == 0 ? 'Las credenciales no coinciden' : '',
                'id'      => $usuario['id'],
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }

        $this->viewBuilder()->setLayout('login');
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Envía y recupera la cuenta del usuario.
     *
     * @return array Json response
     */
    public function recovery()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $email = $this->request->getData('email');
        $code  = 0;

        $usuarioObj = $this->Usuarios->find('byEmail', ['email' => $email]);

        if (!empty($usuarioObj)) {
            $string = $this->String->getRandomString();

            $usuarioObj->password = $string;

            if ($this->Usuarios->save($usuarioObj)) {
                $code     = 1;
                $emailObj = new \Cake\Mailer\Email();
                $emailObj
                    ->setEmailFormat('html')
                    ->setViewVars([
                        'usuario' => trim($usuario),
                        'clave'   => $proyectoObj->clave,
                        'nombre'  => $proyectoObj->nombre,
                    ])
                    ->setFrom(['intranet@edg3web.com' => 'Intranet Ifahto Digital'])
                    ->setTo($email)
                    ->setSubject('Recuperación de Cuenta | Ifahto Digital')
                    ->send('Tu nueva clave es: ' . $string);
            }
        }

        $result = [
            'code'    => $code,
            'message' => empty($usuarioObj)
                ? 'No se ha encontrado el correo electrónico'
                : 'Te hemos enviado tu nueva clave por correo electrónico',
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $params     = $this->request->getData();
            $usuarioObj = $this->Usuarios->find('byEmail', ['email' => $this->request->getData('email')]);
            $code       = 0;
            $message    = 'Ha habido un error al intentar crear al usuario';

            if ($this->request->getData('password') !== $this->request->getData('re_password')) {
                $result = [
                    'code'    => 3,
                    'message' => 'Las contraseñas no coinciden',
                ];

                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode($result));
            }

            if (!empty($usuarioObj)) {
                if ($usuarioObj->estado == 1) {
                    $code    = 2;
                    $message = 'Ya existe un usuario con el mismo correo electrónico';
                } else {
                    $usuarioObj->estado   = 1;
                    $usuarioObj->password = $this->request->getData('password');

                    try {
                        $code = $this->Usuarios->save($usuarioObj) ? 1 : 0;
                    } catch (Exception $e) {
                    }
                }
            } else {
                $usuarioObj      = $this->Usuarios->newEntity($this->request->getData());
                $apellidoMaterno = !empty($this->request->getData('apellido_materno'))
                    ? $this->request->getData('apellido_materno')
                    : '';

                $usuarioObj->apellido_materno = $apellidoMaterno;
                $usuarioObj->costo_hora       = !empty($this->request->getData('costo_hora'))
                    ? $this->request->getData('costo_hora')
                    : 250;

                try {
                    $code = $this->Usuarios->save($usuarioObj) ? 1 : $code;
                } catch (Exception $e) {
                }
            }

            $result = [
                'code'    => $code,
                'message' => $code == 1 ? 'Se ha creado correctamente el usuario' : $message,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Edita un usuario.
     *
     * @param  integer $id Id del usuario
     * @return array       Json response
     */
    public function edit($id)
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Método no encontrado');
        }

        $params     = $this->request->getData();
        $message    = 'Ha habido un error al intentar editar el usuario';
        $usuarioObj = $this->Usuarios->get($id);

        $usuarioObj->puesto_id        = $params['puesto_id'];
        $usuarioObj->perfil_id        = $params['perfil_id'];
        $usuarioObj->nombre           = $params['nombre'];
        $usuarioObj->apellido_paterno = $params['apellido_paterno'];
        $usuarioObj->apellido_materno = !empty($params['apellido_materno']) ? $params['apellido_materno'] : '';
        $usuarioObj->email            = $params['email'];
        $usuarioObj->costo_hora       = !empty($params['costo_hora']) ? $params['costo_hora'] : '';

        try {
            $code = $this->Usuarios->save($usuarioObj) ? 1 : 0;
        } catch (Exception $e) {
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha editado correctamente el usuario' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Elimina un usuario.
     * Recibe por POST el id del usuario.
     *
     * @return array Json response
     */
    public function delete()
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Error al procesar');
        }

        $code       = 0;
        $message    = 'Ha habido un error al intentar eliminar al usuario';
        $usuarioObj = $this->Usuarios->get($this->request->getData('id'));

        if (empty($usuarioObj) || $usuarioObj->estado == 0) {
            $code    = 3;
            $message = 'No existe el usuario';
        } else {
            $usuarioObj->estado = 0;

            try {
                $code = $this->Usuarios->save($usuarioObj) ? 1 : $code;
            } catch (Exception $e) {
            }
        }

        $result = [
            'code'    => $code,
            'message' => $code == 1 ? 'Se ha eliminado correctamente el usuario' : $message,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Guarda las horas laboradas de cada colaborador.
     *
     * @return array Json response
     */
    public function timesheet()
    {
        if ($this->request->is('post')) {
            $this->loadModel('Timesheets');

            $today      = date('Y-m-d');
            $timesheets = json_decode($this->request->getData('timesheet'));
            $fecha      = $this->request->getData('fecha');
            $idUsuario  = $this->Auth->user('id');
            $code       = 1;
            $message    = 'Se han guardado correctamente las horas';

            foreach ($timesheets as $timesheet) {
                $timesheetObj = $this->Timesheets->newEntity();

                $timesheetObj->proyecto_id = $timesheet->proyecto_id;
                $timesheetObj->usuario_id  = $idUsuario;
                $timesheetObj->tipo        = $timesheet->tipo;
                $timesheetObj->fecha       = $fecha;
                $timesheetObj->total       = $timesheet->total_hora;

                if (!$this->Timesheets->save($timesheetObj)) {
                    $code = 0;
                    break;
                }
            }

            $timeObj = new time($fecha);
            $isToday = $timeObj->isToday();

            $timeObj->modify('+1 day');

            $result = [
                'code'           => $code,
                'message'        => $code == 1 ? $message : 'Ha habido un error al intentar guardar las horas',
                'siguienteFecha' => $timeObj->format('Y-m-d'),
                'isToday'        => $isToday,
            ];

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($result));
        }
    }

    /**
     * Devuelve la fecha actual para agregar horas laboradas.
     *
     * @return array Json response
     */
    public function getDateTimesheet()
    {
        $this->loadModel('Timesheets');

        $idUsuario    = $this->Auth->user('id');
        $usuarioObj   = $this->Usuarios->get($idUsuario);
        $timesheetObj = $this->Timesheets
            ->find()
            ->select(['usuario_id', 'fecha'])
            ->where(['usuario_id' => $idUsuario, 'estado' => 1])
            ->order('fecha asc')
            ->last();

        $dateObj           = new Date($timesheetObj->fecha);
        $siguienteFecha    = $dateObj->modify('+1 day');
        $lastTimesheetDate = strtotime($timesheetObj->fecha->format('Y-m-d'));
        $usuarioStartDate  = strtotime($usuarioObj->fecha_inicio->format('Y-m-d'));

        $startDate = ($usuarioStartDate <= $lastTimesheetDate)
            ? $siguienteFecha->format('Y-m-d')
            : $usuarioObj->fecha_inicio->format('Y-m-d');

        $timeObj = new time($lastTimesheetDate);
        $result  = [
            'startDate' => $startDate,
            'isToday'   => $timeObj->isToday(),
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }
}
