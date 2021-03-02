<?php


namespace Aoeng\Laravel\Admin\Filesystem\Fields;


use Aoeng\Laravel\Admin\Filesystem\FilesystemFormField;

class MultipleImageField extends FilesystemFormField
{
    protected $type = 'image';
    protected $multiple = true;

}
