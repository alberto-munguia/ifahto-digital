<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class Usuario extends Entity
{
    /**
     * Devuelve el nombre completo del usuario.
     *
     * @return string Nombre Completo
     */
    protected function _getNombreCompleto()
    {
        $usuario = $this->_properties['nombre'] . ' ' . $this->_properties['apellido_paterno'];

        if (!empty($this->_properties['apellido_materno'])) {
            $usuario .= ' ' . $this->_properties['apellido_materno'];
        }

        return $usuario;
    }

    /**
     * Converte la contraseña en hash.
     *
     * @param  string $password Contraseña
     * @return string           Contraseña encriptada
     */
    protected function _setPassword($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher)->hash($password);
        }
    }
}
