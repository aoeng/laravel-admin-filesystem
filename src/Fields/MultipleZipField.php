<?php


namespace Aoeng\Laravel\Admin\Filesystem\Fields;


use Aoeng\Laravel\Admin\Filesystem\FilesystemFormField;

class MultipleZipField extends FilesystemFormField
{
    protected $type = 'zip';
    protected $multiple = true;

}
