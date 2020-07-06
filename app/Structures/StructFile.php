<?php


namespace App\Structures;


class StructFile
{

    public string $name;
    public string $storage;
    public string $extension;
    public string $mime;
    public string $url;
    public bool $exists = false; // if file exists or is only an instance


    public function __construct($name="", $storage="", $extension="", $mime="", $exists=false)
    {
        $this->name = $name;
        $this->storage = $storage;
        $this->extension = $extension;
        $this->mime = $mime;
        $this->exists = $exists;
    }
}
