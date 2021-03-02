<?php


namespace Aoeng\Laravel\Admin\Filesystem\Fields;


use Aoeng\Laravel\Admin\Filesystem\FilesystemFormField;

class MultipleVideoField extends FilesystemFormField
{
    protected $type = 'video';
    protected $multiple = true;

}
