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
    }

    public function save($resource): StructFile
    {
        $this->file->name = 'file_' . uniqid(md5(Auth::id()) . '_' . time(), true);
        Storage::put($this->file->name, $resource);
        $this->file->exists = true;

        return $this->file;
    }
}
