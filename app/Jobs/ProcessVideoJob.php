<?php

namespace App\Jobs;

use App\Models\PostMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public PostMedia $media) {}

    public function handle(): void
    {
        $originalPath = $this->media->path;

        if (! Storage::disk($this->media->disk)->exists($originalPath)) {
            return;
        }

        $processedPath = 'posts/videos/processed/'.$this->media->id.'.mp4';
        $thumbnailPath = 'posts/videos/thumbs/'.$this->media->id.'.jpg';

        $fullLocalPath = Storage::disk($this->media->disk)->path($originalPath);

        $ffmpeg = config('filesystems.ffmpeg_path', 'ffmpeg');

        $tempDir = sys_get_temp_dir();
        $tempVideo = $tempDir.'/'.$this->media->id.'_processed.mp4';
        $tempThumb = $tempDir.'/'.$this->media->id.'_thumb.jpg';

        $cmd = sprintf(
            '%s -i %s -vf "scale=1280:720:force_original_aspect_ratio=decrease,pad=1280:720:(ow-iw)/2:(oh-ih)/2" -c:v libx264 -preset fast -crf 23 -c:a aac -b:a 128k -movflags +faststart %s 2>&1',
            escapeshellcmd($ffmpeg),
            escapeshellarg($fullLocalPath),
            escapeshellarg($tempVideo)
        );
        exec($cmd, $output, $returnCode);

        $finalVideoPath = $originalPath;

        if ($returnCode === 0 && file_exists($tempVideo)) {
            Storage::disk($this->media->disk)->put($processedPath, file_get_contents($tempVideo));
            unlink($tempVideo);
            $finalVideoPath = $processedPath;
        }

        $thumbCmd = sprintf(
            '%s -i %s -ss 00:00:03 -vframes 1 -q:v 2 %s 2>&1',
            escapeshellcmd($ffmpeg),
            escapeshellarg($fullLocalPath),
            escapeshellarg($tempThumb)
        );
        exec($thumbCmd, $thumbOutput, $thumbReturnCode);

        $finalThumbnailPath = null;

        if ($thumbReturnCode === 0 && file_exists($tempThumb)) {
            Storage::disk($this->media->disk)->put($thumbnailPath, file_get_contents($tempThumb));
            unlink($tempThumb);
            $finalThumbnailPath = $thumbnailPath;
        }

        $this->media->update([
            'path' => $finalVideoPath,
            'thumbnail_path' => $finalThumbnailPath,
        ]);
    }
}
