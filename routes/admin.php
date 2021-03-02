<?php
Route::get('filesystem', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@index')->name('filesystem-index');
Route::post('filesystem/upload', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@upload')->name('filesystem-upload');
Route::delete('filesystem/delete', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@delete')->name('filesystem-delete');
Route::post('filesystem/download', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@download')->name('filesystem-download');
Route::put('filesystem/rename', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@rename')->name('filesystem-rename');
Route::get('filesystem/field', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@field')->name('filesystem-field');
Route::get('filesystem/sts', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@getSts')->name('filesystem-sts');
Route::post('filesystem/save', 'Aoeng\Laravel\Admin\Filesystem\Controllers\FilesystemController@save')->name('filesystem-save');
