<?php

namespace Aoeng\Laravel\Admin\Filesystem;

use Encore\Admin\Extension;

class Filesystem extends Extension
{
    public $name = 'filesystem';

    public $views = __DIR__ . '/../resources/views';

    public $assets = __DIR__ . '/../resources/assets';


    public function __construct()
    {
        self::routes(__DIR__ . '/../routes/admin.php');
    }

    public static function import()
    {
        parent::import();

        self::createMenu('文件管理', 'filesystem', 'fa-file');
    }
}
