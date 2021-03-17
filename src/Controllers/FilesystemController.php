<?php


namespace Aoeng\Laravel\Admin\Filesystem\Controllers;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Aoeng\Laravel\Admin\Filesystem\Models\Filesystem;
use Aoeng\Laravel\Support\Traits\ResponseJsonActions;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesystemController extends Controller
{
    use ResponseJsonActions;

    public function index(Request $request): \Encore\Admin\Layout\Content
    {
        return Admin::content(function (Content $content) use ($request) {
            $content->header('Media manager');

            $view = $request->get('view', 'table');

            $files = Filesystem::query()
                ->when($request->filled('type'), function ($query) use ($request) {
                    $query->where('type', $request->input('type'));
                })
                ->when($request->filled('disk'), function ($query) use ($request) {
                    $query->where('disk', $request->input('disk'));
                })
                ->when($request->filled('name'), function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->input('disk')}%");
                })
                ->orderByDesc('created_at')
                ->paginate();


            $content->body(view("laravel-admin-filesystem::$view", compact('files')));
        });
    }


    public function upload(Request $request)
    {
        foreach ($request->file('file') as $file) {
            $filesystem = new Filesystem();

            $filesystem->name = $file->getClientOriginalName();
            $filesystem->type = Str::before($file->getClientMimeType(), '/');
            $filesystem->ext = $file->getClientOriginalExtension();
            $filesystem->disk = config('filesystems.default');
            $filesystem->size = $file->getSize();
            $filesystem->path = $file->store('files/' . $filesystem->type . '/' . date('Y/m/d'));
            $filesystem->save();
        }

        if ($request->filled('is_ajax')) {
            return $this->success();
        }
        return back();
    }

    public function save(Request $request)
    {
        $filesystem = new Filesystem();

        $filesystem->name = $request->input('name');
        $filesystem->type = $request->input('type');
        $filesystem->ext = $request->input('ext');
        $filesystem->disk = config('filesystems.default');
        $filesystem->size = $request->input('size');
        $filesystem->path = $request->input('path');
        $filesystem->save();

        return $this->success();
    }

    public function delete(Request $request)
    {
        Filesystem::query()->whereIn('id', $request->input('id'))->delete();
        return $this->success();
    }

    public function download(Request $request)
    {
        $file = Filesystem::query()->find($request->input('id'));
        return Storage::disk($file->disk)->download($file->path, $file->name);
    }

    public function rename(Request $request)
    {
        $request->filled('name') && Filesystem::query()->where('id', $request->input('id'))->update(['name' => $request->input('name')]);

        return $this->success();
    }

    public function field(Request $request)
    {
        return Filesystem::query()
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('disk'), function ($query) use ($request) {
                $query->where('disk', $request->input('disk'));
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->input('disk')}%");
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                $query->orderBy($request->input('sort'), $request->input('order', 'desc'));
            }, function ($query) {
                $query->orderByDesc('created_at');
            })
            ->paginate();
    }

    public function getSts(Request $request)
    {
        AlibabaCloud::accessKeyClient(config('filesystems.disks.oss.access_id'), config('filesystems.disks.oss.access_key'))
            ->regionId(config('filesystems.disks.oss.region_id'))
            ->verify(false)
            ->asDefaultClient();
        try {
            $response = AlibabaCloud::rpc()
                ->product('Sts')
                ->scheme('https') // https | http
                ->version('2015-04-01')
                ->action('AssumeRole')
                ->method('POST')
                ->host('sts.aliyuncs.com')
                ->verify(false)
                ->options([
                    'query' => [
                        'RoleArn'         => config('filesystems.disks.oss.sts_role_arn'),
                        'RoleSessionName' => "oss",
                    ]
                ])
                ->request();

            if (!$response->isSuccess()) {
                return $this->error('获取STS授权失败');
            }

        } catch (ClientException $clientException) {
            return $this->error($clientException->getMessage());
        } catch (ServerException $serverException) {
            return $this->error($serverException->getMessage() . $serverException->getErrorCode() . $serverException->getRequestId() . $serverException->getErrorMessage());
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
        $response = $response->toArray();


        return $this->responseJson([
            'sts' => [
                'SecurityToken'   => $response['Credentials']['SecurityToken'],
                'AccessKeySecret' => $response['Credentials']['AccessKeySecret'],
                'AccessKeyId'     => $response['Credentials']['AccessKeyId']
            ],
            'oss' => [
                'endpoint' => config('filesystems.disks.oss.endpoint'),
                'bucket'   => config('filesystems.disks.oss.bucket'),
                'region'   => config('filesystems.disks.oss.region_id'),
            ]
        ]);
    }
}
