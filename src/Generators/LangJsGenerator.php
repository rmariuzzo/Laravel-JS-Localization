<?php namespace Mariuzzo\LaravelJsLocalization\Generators;

use Illuminate\Filesystem\Filesystem as File;

class LangJsGenerator
{
    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function make($target)
    {
        $path = app_path().'/lang';

        if ( ! $this->file->exists($path))
        {
            throw new Exception("${path} doesn't exists!");
        }

        $files = $this->file->allFiles($path);
        var_dump($files);
    }
}
