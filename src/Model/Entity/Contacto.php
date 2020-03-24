<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class Contacto extends Entity
{
	/**
     * Devuelve el nombre completo del contacto.
     *
     * @return string Nombre Completo
     */
    protected function _getNombreCompleto()
    {
        $contacto = $this->_properties['nombre'] . ' ' . $this->_properties['apellido_paterno'];

        if (!empty($this->_properties['apellido_materno'])) {
            $contacto .= ' ' . $this->_properties['apellido_materno'];
        }

        return trim($contacto);
    }
}
