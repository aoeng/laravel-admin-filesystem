<?php

namespace Aoeng\Laravel\Admin\Filesystem\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property false|mixed|string path
 * @property int|mixed size
 * @property string disk
 * @property mixed|string ext
 * @property mixed|string type
 * @property mixed|string name
 */
class Filesystem extends Model
{
    use HasFactory, SoftDeletes,DefaultDatetimeFormat;

    protected $appends = ['url', 'file_size'];

    protected $fileTypes = [
        'image' => 'png|jpg|jpeg|tmp|gif',
        'word'  => 'doc|docx',
        'ppt'   => 'ppt|pptx',
        'pdf'   => 'pdf',
        'code'  => 'php|js|java|python|ruby|go|c|cpp|sql|m|h|json|html|aspx',
        'zip'   => 'zip|tar\.gz|rar|rpm',
        'txt'   => 'txt|pac|log|md',
        'audio' => 'mp3|wav|flac|3pg|aa|aac|ape|au|m4a|mpc|ogg',
        'video' => 'mkv|rmvb|flv|mp4|avi|wmv|rm|asf|mpeg',
    ];


    public function getUrlAttribute()
    {
        try {
            return empty($this->path) ? '' : Storage::disk($this->disk)->url($this->path);
        } catch (\Exception $exception) {
            return '';
        }
    }

    public function getFileSizeAttribute()
    {
        $size= $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
