<?php


namespace App\Services\File;


use App\Contracts\File;
use App\Structures\StructFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Disk implements File
{

    /**
     * @var StructFile
     */
    private StructFile $file;

    public function __construct(StructFile $file)
    {
        $this->file = $file;
        $this->file->storage = 'disk';
    }

    public function save($resource): StructFile
    {
        $this->file->name = 'file_' . uniqid(md5(Auth::id()) . '_' . time(), true) . '.jpg';
        Storage::put('images/' . $this->file->name, base64_decode($resource));

        $this->file->exists = true;
        $this->file->mime = 'image/jpeg';
        $this->file->extension = 'jpg';
        $this->file->url = url('/images/' . $this->file->name);

        return $this->file;
    }
}
