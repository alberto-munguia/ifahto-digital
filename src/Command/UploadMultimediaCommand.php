<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * Upload command.
 */
class UploadMultimediaCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/3.0/en/console-and-shells/commands.html#defining-arguments-and-options
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $S3Table         = TableRegistry::get('S3');
        $multimediaTable = TableRegistry::get('Multimedias');
        $multimedias     = $multimediaTable->find()->where(['version_id' => '', 'estado' => 1]);

        foreach ($multimedias as $multimediaObj) {
            $url      = $multimediaObj->url;
            $filename = '/var/www/miller.edge.com/public_html/webroot' . $url;

            if (!file_exists($filename)) {
                continue;
            }

            $io->info('File: ' . $filename . "\n");

            $archivo = strtolower(str_replace(' ', '-', $multimediaObj->nombre_archivo));
            $content = $S3Table->putObject($archivo, '', [
                'SourceFile' => $filename,
                'ACL'        => 'public-read',
            ]);

            $multimediaObj->url        = $content['ObjectURL'];
            $multimediaObj->version_id = $content['VersionId'];

            try {
                $result = $multimediaTable->save($multimediaObj) ? true : false;
            } catch (Exception $e) {
            }

            if ($result) {
                $io->success('Borrando archivo: ' . $filename . "\n\n");
                unlink($filename);
            }
        }

        return true;
    }
}
