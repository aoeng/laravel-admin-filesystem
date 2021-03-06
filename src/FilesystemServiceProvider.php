<?php


namespace Aoeng\Laravel\Admin\Filesystem;


use Aoeng\Laravel\Admin\Filesystem\Fields\ApkField;
use Aoeng\Laravel\Admin\Filesystem\Fields\AudioField;
use Aoeng\Laravel\Admin\Filesystem\Fields\FileField;
use Aoeng\Laravel\Admin\Filesystem\Fields\ImageField;
use Aoeng\Laravel\Admin\Filesystem\Fields\MultipleApkField;
use Aoeng\Laravel\Admin\Filesystem\Fields\MultipleAudioField;
use Aoeng\Laravel\Admin\Filesystem\Fields\MultipleFileField;
use Aoeng\Laravel\Admin\Filesystem\Fields\MultipleImageField;
use Aoeng\Laravel\Admin\Filesystem\Fields\MultipleVideoField;
use Aoeng\Laravel\Admin\Filesystem\Fields\MultipleZipField;
use Aoeng\Laravel\Admin\Filesystem\Fields\VideoField;
use Aoeng\Laravel\Admin\Filesystem\Fields\ZipField;
use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    public function boot(Filesystem $extension)
    {
        if (!Filesystem::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-filesystem');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {

            $this->publishes([
                $assets => public_path('vendor/aoeng/laravel-admin-filesystem')
            ], 'laravel-admin-filesystem');
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Admin::booting(function () {
            Form::extend('image', ImageField::class);
            Form::extend('multipleImage', MultipleImageField::class);
            Form::extend('file', FileField::class);
            Form::extend('multipleFile', MultipleFileField::class);
            Form::extend('video', VideoField::class);
            Form::extend('multipleVideo', MultipleVideoField::class);
            Form::extend('audio', AudioField::class);
            Form::extend('multipleAudio', MultipleAudioField::class);
            Form::extend('zip', ZipField::class);
            Form::extend('multipleZip', MultipleZipField::class);
            Form::extend('apk', ApkField::class);
            Form::extend('multipleApk', MultipleApkField::class);
        });
    }

    public function register()
    {

    }

    protected function registerRoutes()
    {

    }

}
