<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class FileUtil
{
    public function getFile($filename)
    {
        $path = Storage::disk('mfa_ext')->path($filename);
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        return response()->file($path);
    }
}
