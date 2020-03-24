<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class MultimediaComponent extends Component
{
    public $components = ['String'];

    /**
     * Sube archivos al sistema.
     *
     * @param  string         $type Tipo de documento
     * @param  array          $file Datos del archivo
     * @return boolean|object       False|Multimedia
     */
    public function uploadFile($type, array $file)
    {
        if (empty($file)) {
            return false;
        }

        $S3Table         = TableRegistry::get('S3');
        $multimediaTable = TableRegistry::get('Multimedias');
        $archivo         = strtolower(str_replace(' ', '-', $file['name']));

        $content = $S3Table->putObject($archivo, '', [
            'SourceFile' => $file['tmp_name'],
            'ACL'        => 'public-read',
        ]);

        $multimedia                 = $multimediaTable->newEntity();
        $multimedia->nombre_archivo = $file['name'];
        $multimedia->url            = $content['ObjectURL'];
        $multimedia->version_id     = $content['VersionId'];
        $multimedia->tipo_archivo   = $file['type'];

        try {
            $multimediaObj = $multimediaTable->save($multimedia);
        } catch (Exception $e) {
        }

        return $multimediaObj;
    }
}
