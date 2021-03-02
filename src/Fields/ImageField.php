<?php


namespace Aoeng\Laravel\Admin\Filesystem\Fields;


use Aoeng\Laravel\Admin\Filesystem\FilesystemFormField;

class ImageField extends FilesystemFormField
{
    protected $type = 'image';
    protected $multiple = false;

}
