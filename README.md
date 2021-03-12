laravel-admin extension 文件管理
======

- 包含了大文件切片上传
- 基于aliyun OSS

## 安装
```bash
composer require  aoeng/laravel-admin-filesystem
php artisan vendor:publish --provider="Aoeng\Laravel\Admin\Filesystem\FilesystemServiceProvider"
```
## 推荐安装aliyun OSS扩展

[aoeng/laravel-aliyun-oss ](https://github.com/aoeng/laravel-aliyun-oss )

## 参考

[de-memory/media-selector](https://github.com/de-memory/media-selector)
