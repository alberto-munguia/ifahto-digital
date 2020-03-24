<?php

namespace App\Model\Table;

use CakeS3\Datasource\AwsS3Table;

/**
 * @author José Alberto Munguia Olmos <munguiaolmos.alberto@gmail.com>
 */
class S3Table extends AwsS3Table
{
    protected static $_connectionName = 'AwsS3Connection';
}