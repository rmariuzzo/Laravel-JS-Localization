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
        $messages = $this->getMessages();
        return $this->file->put($target, json_encode($messages));
    }

    protected function getMessages()
    {
        $messages = array();
        $path = app_path().'/lang';

        if ( ! $this->file->exists($path))
        {
            throw new \Exception("${path} doesn't exists!");
        }

        foreach ($this->file->allFiles($path) as $file) {

            $pathName = $file->getRelativePathName();

            if ( $this->file->extension($pathName) !== 'php' ) continue;

            $key = str_replace('/', '.', substr($pathName, 0, -4));

            $messages[ $key ] = include "${path}/${pathName}";
        }

        return $messages;
    }
}
