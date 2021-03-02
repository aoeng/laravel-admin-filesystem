<?php


namespace Aoeng\Laravel\Admin\Filesystem\Fields;


use Aoeng\Laravel\Admin\Filesystem\FilesystemFormField;

class MultipleApkField extends FilesystemFormField
{
    protected $type = 'apk';
    protected $multiple = true;

}
