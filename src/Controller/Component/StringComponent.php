<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class StringComponent extends Component
{
    /**
     * Devuelve una cadena aleatoria.
     *
     * @param  integer $total Total caracteres
     * @return string         Cadena
     */
    public function getRandomString($total = 10)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $total; $i++) {
            $index         = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index]; 
        } 
  
        return $randomString;
    }
}
