<?php

namespace Aoeng\Laravel\Admin\Filesystem;

use Encore\Admin\Form\Field;
use Illuminate\Support\Facades\Storage;

class FilesystemFormField extends Field
{
    protected $view = 'laravel-admin-filesystem::form-field';

    protected static $css = [
        'vendor/aoeng/laravel-admin-filesystem/bootstrap-table/dist/bootstrap-table.min.css',
        'vendor/aoeng/laravel-admin-filesystem/main.css',

    ];

    protected static $js = [
        'vendor/aoeng/laravel-admin-filesystem/bootstrap-table/dist/bootstrap-table.min.js',
        'vendor/aoeng/laravel-admin-filesystem/bootstrap-table/dist/locale/bootstrap-table-zh-CN.min.js',
        'vendor/aoeng/laravel-admin-filesystem/sortablejs/Sortable.js',
        'vendor/aoeng/laravel-admin-filesystem/sortablejs/st/prettify/prettify.js',
        'vendor/aoeng/laravel-admin-filesystem/sortablejs/st/prettify/run_prettify.js',
        'vendor/aoeng/laravel-admin-filesystem/aliyun-oss-sdk.min.js',
        'vendor/aoeng/laravel-admin-filesystem/main.js',
    ];

    /**
     * 媒体选择最大文件数
     *
     * @var int
     */
    protected $multiple = false;

    /**
     * 媒体选择类型
     *
     * blend            混合选择
     * image            图片选择
     * video            视频选择
     * audio            音频选择
     * powerpoint       文稿选择
     * code             代码文件选择
     * zip              压缩包选择
     * text             文本选择
     * other            其他选择
     *
     * @var string
     */
    protected $type = 'blend';

    /**
     * 拖动排序
     *
     * @var bool
     */
    protected $sortable = true;

    protected $selectList = [
        'image'      => '图片',
        'video'      => '视频',
        'audio'      => '音频',
        'powerpoint' => '文稿',
        'code'       => '代码',
        'zip'        => '压缩包',
        'text'       => '文本',
        'apk'        => '安装包',
        'other'      => '其它',
    ];

    /**
     * 初始化
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string|void
     */
    public function render()
    {
        // 向视图添加变量
        $this->addVariables([
            'multiple'   => $this->multiple,
            'type'       => $this->type,
            'selectList' => $this->selectList,
        ]);

        $label = $this->label;
        $name = $this->column;
        $type = $this->type;
        $sortable = $this->sortable;
        $diskName = config('filesystems.default');

        $disk = json_encode(config('filesystems.disks.' . $diskName, []));

        $this->script = "
            if(!window.Demo{$name}){
                window.Demo{$name} = new MediaSelector(
                   '{$label}','{$name}','{$type}','{$this->multiple}','{$sortable}','{$disk}'
                );
                Demo{$name}.run();
            }
            Demo{$name}.init();
            Demo{$name}.getMediaList();
            Demo{$name}.sortable();
        ";

        return parent::render();
    }
}
