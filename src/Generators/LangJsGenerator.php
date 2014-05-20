<?php namespace Mariuzzo\LaravelJsLocalization\Generators;

use Illuminate\Filesystem\Filesystem as File;
use JShrink\Minifier;

class LangJsGenerator
{
    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function make($target, $options)
    {
        $messages = $this->getMessages();
        $this->prepareTarget($target);

        $template = $this->file->get(__DIR__ . '/Templates/langjs_with_messages.js');
        $langjs = $this->file->get(__DIR__ . '/../../js/lang.js');

        $template = str_replace('\'{ messages }\'', json_encode($messages), $template);
        $template = str_replace('\'{ langjs }\';', $langjs, $template);

        if ($options['compress'])
        {
            $template = Minifier::minify($template);
        }

        return $this->file->put($target, $template);
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

            $key = substr($pathName, 0, -4);
            $key = str_replace('\\', '.', $key);
            $key = str_replace('/', '.', $key);

            $messages[ $key ] = include "${path}/${pathName}";
        }

        return $messages;
    }

    protected function prepareTarget($target)
    {
        $dirname = dirname($target);

        if ( ! $this->file->exists($dirname) )
        {
            $this->file->makeDirectory($dirname);
        }
    }
}
