<?php


namespace Aoeng\Laravel\Admin\Filesystem\Fields;


use Aoeng\Laravel\Admin\Filesystem\FilesystemFormField;

class MultipleAudioField extends FilesystemFormField
{
    protected $type = 'audio';
    protected $multiple = true;

}
