<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('上传', description: '文件上传与 OSS 签名', weight: 40)]
#[Prefix('upload')]
class UploadController extends Controller
{
    #[Post('image', middleware: ['auth:sanctum'])]
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:10240',
            'directory' => 'nullable|string',
        ]);

        $directory = $request->input('directory', 'uploads/images');
        $disk = config('filesystems.default');
        $path = $request->file('image')->store($directory, $disk);

        return $this->success([
            'disk' => $disk,
            'path' => $path,
            'url' => Storage::url($path),
        ], '上传成功');
    }

    #[Get('signature', middleware: ['auth:sanctum'])]
    public function signature(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:image,video',
            'directory' => 'nullable|string',
        ]);

        $directory = $request->input('directory', 'uploads/'.$request->input('type').'s');
        $disk = config('filesystems.disks.oss');

        $policy = json_encode([
            'expiration' => now()->addMinutes(10)->format('Y-m-d\TH:i:s.000\Z'),
            'conditions' => [
                ['content-length-range', 0, 500 * 1024 * 1024],
                ['starts-with', '$key', $directory.'/'],
            ],
        ]);

        $signature = base64_encode(hash_hmac('sha1', base64_encode($policy), $disk['access_key_secret'], true));

        return $this->success([
            'access_key_id' => $disk['access_key_id'],
            'policy' => base64_encode($policy),
            'signature' => $signature,
            'host' => 'https://'.$disk['bucket'].'.'.$disk['endpoint'],
            'expire' => 600,
            'directory' => $directory,
        ]);
    }
}
