<?php namespace Mariuzzo\LaravelJsLocalization\Generators;

use Illuminate\Filesystem\Filesystem as File;
use JShrink\Minifier;

/**
 * The LangJsGenerator class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsGenerator
{
    /**
     * The file service.
     */
    protected $file;

    /**
     * The source path of the language files.
     */
    protected $sourcePath;

    /**
     * List of messages should be included in build.
     *
     * @var array
     */
    protected $messagesIncluded = [];

    /**
     * Construct a new LangJsGenerator instance.
     *
     * @param Illuminate\Filesystem\File $file       The file service instance.
     * @param string                     $sourcePath The source path of the language files.
     */
    public function __construct(File $file, $sourcePath)
    {
        $this->file = $file;
        $this->sourcePath = $sourcePath;
        $this->collectMessagesIncluded();
    }

    /**
     * Generate a JS lang file from all language files.
     *
     * @param string $target  The target directory.
     * @param array  $options Array of options.
     */
    public function generate($target, $options)
    {
        $messages = $this->getMessages();
        $this->prepareTarget($target);

        $template = $this->file->get(__DIR__ . '/Templates/langjs_with_messages.js');
        $langjs = $this->file->get(__DIR__ . '/../../../../Lang.js/src/lang.js');

        $template = str_replace('\'{ messages }\'', json_encode($messages), $template);
        $template = str_replace('\'{ langjs }\';', $langjs, $template);

        if ($options['compress'])
        {
            $template = Minifier::minify($template);
        }

        return $this->file->put($target, $template);
    }

    /**
     * Return all language messages.
     *
     * @return array
     */
    protected function getMessages()
    {
        $messages = array();
        $path = $this->sourcePath;

        if ( ! $this->file->exists($path))
        {
            throw new \Exception("${path} doesn't exists!");
        }

        foreach ($this->file->allFiles($path) as $file)
        {
            $pathName = $file->getRelativePathName();

            if ( $this->file->extension($pathName) !== 'php' ) continue;

            if ($this->isMessagesExcluded($pathName)) {
                continue;
            }

            $key = substr($pathName, 0, -4);
            $key = str_replace('\\', '.', $key);
            $key = str_replace('/', '.', $key);

            $messages[ $key ] = include "${path}/${pathName}";
        }

        return $messages;
    }

    /**
     * Prepare the target directoy.
     *
     * @param string $target The target directory.
     */
    protected function prepareTarget($target)
    {
        $dirname = dirname($target);

        if ( ! $this->file->exists($dirname) )
        {
            $this->file->makeDirectory($dirname, null, true);
        }
    }

    /**
     * If messages should be excluded from build.
     *
     * @param string $filePath
     * @return bool
     */
    protected function isMessagesExcluded($filePath)
    {
        if (empty($this->messagesIncluded)) {
            return false;
        }

        $localeDirSeparatorPosition = strpos($filePath, '/');
        $filePath = substr($filePath, $localeDirSeparatorPosition);
        $filePath = ltrim($filePath, '/');
        $filePath = substr($filePath, 0, -4);

        if (in_array($filePath, $this->messagesIncluded)) {
            return false;
        }

        return true;
    }

    /**
     * Prepare list of messages should be included in build.
     */
    protected function collectMessagesIncluded()
    {
        $this->messagesIncluded = config('localization-js.messages');
    }
}
