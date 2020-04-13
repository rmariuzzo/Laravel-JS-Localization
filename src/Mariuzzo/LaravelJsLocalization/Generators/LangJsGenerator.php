<?php

namespace Mariuzzo\LaravelJsLocalization\Generators;

use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Str;
use JShrink\Minifier;

/**
 * The LangJsGenerator class.
 *
 * @author  Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsGenerator
{
    /**
     * The file service.
     *
     * @var File
     */
    protected $file;

    /**
     * The source path of the language files.
     *
     * @var string
     */
    protected $sourcePath;

    /**
     * List of messages should be included in build.
     *
     * @var array
     */
    protected $messagesIncluded = [];


    /**
     * List of files/folders the autodetector should search through.
     *
     * @var array
     */
    public $keepMessages = [];

    /**
     * Name of the domain in which all string-translation should be stored under.
     * More about string-translation: https://laravel.com/docs/master/localization#retrieving-translation-strings
     *
     * @var string
     */
    protected $stringsDomain = 'strings';

    /**
     * Construct a new LangJsGenerator instance.
     *
     * @param File   $file       The file service instance.
     * @param string $sourcePath The source path of the language files.
     * @param array $messagesIncluded List of messages should be included in build.
     */
    public function __construct(File $file, $sourcePath, $messagesIncluded = [])
    {
        $this->file = $file;
        $this->sourcePath = $sourcePath;
        $this->messagesIncluded = $messagesIncluded;
    }

    /**
     * Generate a JS lang file from all language files.
     *
     * @param string $target  The target directory.
     * @param array  $options Array of options.
     *
     * @return int
     */
    public function generate($target, $options)
    {
        if ($options['source']) {
            $this->sourcePath = $options['source'];
        }

        $messages = $this->getMessages($options['no-sort']);
        if ($options['autodetect']) {
            print_r($this->keepMessages);
            $this->filterKeepMessages($messages,$this->keepMessages);
        }
        $this->prepareTarget($target);

        if ($options['no-lib']) {
            $template = $this->file->get(__DIR__.'/Templates/messages.js');
        } else if ($options['json']) {
            $template = $this->file->get(__DIR__.'/Templates/messages.json');
        } else {
            $template = $this->file->get(__DIR__.'/Templates/langjs_with_messages.js');
            $langjs = $this->file->get(__DIR__.'/../../../../lib/lang.min.js');
            $template = str_replace('\'{ langjs }\';', $langjs, $template);
        }

        $template = str_replace('\'{ messages }\'', json_encode($messages), $template);

        if ($options['compress']) {
            $template = Minifier::minify($template);
        }

        return $this->file->put($target, $template);
    }

    /**
     * Recursively sorts all messages by key.
     *
     * @param array $messages The messages to sort by key.
     */
    protected function sortMessages(&$messages)
    {
        if (is_array($messages)) {
            ksort($messages);

            foreach ($messages as $key => &$value) {
                $this->sortMessages($value);
            }
        }
    }

    /**
     * Returns array of filenames from input glob's.
     * @param array $globs Globs to parse
     * @return array
     */
    public function usageSearchFiles($globs)
    {
        $files = [];
        foreach ($globs as $glob) {
            $files += $this->file->glob($this->sourcePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$glob);
        }
        return $files;
    }

    /**
     * Searches a file for Lang.get / Lang.has / Lang.choice etc. occurances.
     * Stores an occurence in $this->keepMessages;
     * @param string $file Absolute path of file to open
     * @return void
     */
    public function usageSearch($file)
    {
        try {
            $content = $this->file->get($file);
            preg_match_all("/Lang\.(?:get|has|choice|trans|transChoice)\(['\"]([^'\"]+)/", $content, $matches);
            foreach ($matches[1] as $match) {
                $chain = [];
                $ref = &$chain;
                foreach (explode(".", $match) as $value) {
                    $ref[$value] = [];
                    $ref = &$ref[$value];
                }
                $this->keepMessages = array_merge_recursive($this->keepMessages, $chain);
                $this->keepMessages[$this->stringsDomain][$match] = "";
            }
        } catch (\Exception $exception) {
            return;
        }
    }

    /**
     * Recursively executes array_intersect_key($array1, $array2);
     * @param array $array1 Array of master keys
     * @param array $array2 Array to check keys against
     * @see array_intersect_key()
     * @return array
     */
    protected function array_intersect_key_recursive( $array1, $array2 ) {
        $array1 = array_intersect_key( $array1, $array2 );
        foreach ( $array1 as $key => $value ) {
            if ( is_array( $value ) && is_array( $array2[ $key ] ) ) {
                $array1[ $key ] = $this->array_intersect_key_recursive( $value, $array2[ $key ] );
            }
        }
        return $array1;
    }

    /**
     * Filters language keys in $messages to only keep keys in $this->keepMessages;
     * @param array &$messages Array of master keys
     * @param array &$keep Array of keys to keep
     * @return void
     */
    protected function filterKeepMessages(&$messages, &$keep){
        $messages = array_filter($messages,function($key) use ($keep) {
            if(array_key_exists($key,$keep)||array_key_exists(substr(strstr($key, '.'),1),$keep)){
                return true;
            }
            return false;
        },ARRAY_FILTER_USE_KEY);
        foreach($messages as $key=>$array){
            $skey = substr(strstr($key, '.'),1);
            $messages[$key] = $this->array_intersect_key_recursive($array,$keep[$skey]);
        }
    }

    /**
     * Return all language messages.
     *
     * @param bool $noSort Whether sorting of the messages should be skipped.
     * @return array
     *
     * @throws \Exception
     */
    protected function getMessages($noSort)
    {
        $messages = [];
        $path = $this->sourcePath;

        if (!$this->file->exists($path)) {
            throw new \Exception("${path} doesn't exists!");
        }

        foreach ($this->file->allFiles($path) as $file) {
            $pathName = $file->getRelativePathName();
            $extension = $this->file->extension($pathName);
            if ($extension != 'php' && $extension != 'json') {
                continue;
            }

            if ($this->isMessagesExcluded($pathName)) {
                continue;
            }

            $key = substr($pathName, 0, -4);
            $key = str_replace('\\', '.', $key);
            $key = str_replace('/', '.', $key);

            if (Str::startsWith($key, 'vendor')) {
                $key = $this->getVendorKey($key);
            }

            $fullPath = $path.DIRECTORY_SEPARATOR.$pathName;
            if ($extension == 'php') {
                $messages[$key] = include $fullPath;
            } else {
                $key = $key.$this->stringsDomain;
                $fileContent = file_get_contents($fullPath);
                $messages[$key] = json_decode($fileContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new InvalidArgumentException('Error while decode ' . basename($fullPath) . ': ' . json_last_error_msg());
                }
            }
        }

        if (!$noSort)
        {
            $this->sortMessages($messages);
        }

        return $messages;
    }

    /**
     * Prepare the target directory.
     *
     * @param string $target The target directory.
     */
    protected function prepareTarget($target)
    {
        $dirname = dirname($target);

        if (!$this->file->exists($dirname)) {
            $this->file->makeDirectory($dirname, 0755, true);
        }
    }

    /**
     * If messages should be excluded from build.
     *
     * @param string $filePath
     *
     * @return bool
     */
    protected function isMessagesExcluded($filePath)
    {
        if (empty($this->messagesIncluded)) {
            return false;
        }

        $filePath = str_replace(DIRECTORY_SEPARATOR, '/', $filePath);

        $localeDirSeparatorPosition = strpos($filePath, '/');
        $filePath = substr($filePath, $localeDirSeparatorPosition);
        $filePath = ltrim($filePath, '/');
        $filePath = substr($filePath, 0, -4);

        if (in_array($filePath, $this->messagesIncluded)) {
            return false;
        }

        return true;
    }

    private function getVendorKey($key)
    {
        $keyParts = explode('.', $key, 4);
        unset($keyParts[0]);

        return $keyParts[2] .'.'. $keyParts[1] . '::' . $keyParts[3];
    }
}
