<?php


namespace Aoeng\Laravel\Admin\Filesystem\Fields;


use Aoeng\Laravel\Admin\Filesystem\FilesystemFormField;

class MultipleImageBoxField extends FilesystemFormField
{
    protected $type = 'image';
    protected $multiple = true;

}
