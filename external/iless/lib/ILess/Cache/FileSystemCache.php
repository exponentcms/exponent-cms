<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Cache;

use ILess\Exception\CacheException;
use LogicException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Cache which stores the contents to files.
 */
class FileSystemCache extends Cache
{
    /**
     * Read data flag.
     */
    const READ_DATA = 1;

    /**
     * Read timeout flag.
     */
    const READ_TIMEOUT = 2;

    /**
     * Last modified flag.
     */
    const READ_LAST_MODIFIED = 4;

    /**
     * Hash flag.
     */
    const READ_HASH = 8;

    /**
     * Separator.
     */
    const SEPARATOR = ':';

    /**
     * Array of options.
     *
     * @var array
     */
    protected $defaultOptions = [
        'ttl' => 86400,
        'suffix' => '.cache',
    ];

    /**
     * Constructor.
     *
     * @param array|string $options Array of options or cache dir path as string
     *
     * @throws LogicException If "cache_dir" option is missing
     * @throws CacheException If the cache directory does not exist or is not writable.
     */
    public function __construct($options = [])
    {
        // this is a cache directory
        if (is_string($options)) {
            $options = [
                'cache_dir' => $options,
            ];
        }
        parent::__construct($options);
    }

    /**
     * Setups the driver.
     *
     * @throws LogicException If the cache_dir option is missing
     * @throws CacheException If the cache directory does not exist or is not writable.
     */
    protected function setup()
    {
        if (!$cacheDir = $this->getOption('cache_dir')) {
            throw new LogicException('Missing "cache_dir" option.');
        }

        // remove last DIRECTORY_SEPARATOR
        if (DIRECTORY_SEPARATOR == substr($cacheDir, -1)) {
            $cacheDir = substr($cacheDir, 0, -1);
            $this->setOption('cache_dir', $cacheDir);
        }

        $this->setupCacheDir($cacheDir);
    }

    /**
     * Setups the cache directory.
     *
     * @param string $cacheDir
     *
     * @throws CacheException If the cache directory does not exist or is not writable.
     *
     * @return true If the setup was successful
     */
    protected function setupCacheDir($cacheDir)
    {
        // create cache dir if needed
        if (!is_dir($cacheDir)) {
            $current_umask = umask(0000);
            if (@mkdir($cacheDir, 0777, true) === false) {
                throw new CacheException(sprintf('The cache directory "%s" could not be created.', $cacheDir));
            }
            umask($current_umask);
        } elseif (!is_writable($cacheDir)) {
            throw new CacheException(sprintf('The cache directory "%s" is not writable.', $cacheDir));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function has($cacheKey)
    {
        $path = $this->getFilePath($cacheKey);

        return file_exists($path) && $this->isValid($path);
    }

    /**
     * {@inheritdoc}
     */
    public function get($cacheKey)
    {
        $file_path = $this->getFilePath($cacheKey);

        if (!file_exists($file_path)) {
            return;
        }

        $data = $this->read($file_path, self::READ_DATA);

        if ($data[self::READ_DATA] === null) {
            return;
        }

        return $this->unserialize($data[self::READ_DATA]);
    }

    /**
     * {@inheritdoc}
     */
    public function set($cacheKey, $data, $ttl = null)
    {
        return $this->write($this->getFilePath($cacheKey), $this->serialize($data), time() + $this->getLifetime($ttl));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cacheKey)
    {
        return @unlink($this->getFilePath($cacheKey));
    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        if (!is_dir($this->getOption('cache_dir'))) {
            return true;
        }

        $result = true;
        $extension = $this->getOption('suffix');
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->getOption('cache_dir'))) as $file) {
            /* @var $file SplFileInfo */
            if ($file->isDir()) {
                continue;
            } else {
                if ($extension == ('.' . pathinfo($file->getFilename(), PATHINFO_EXTENSION))) {
                    $result = @unlink($file) && $result;
                }
            }
        }

        return $result;
    }

    /**
     * Converts a cache key to a full path.
     *
     * @param string $key The cache key
     *
     * @return string The full path to the cache file
     */
    protected function getFilePath($key)
    {
        return $this->getOption('cache_dir') .
        DIRECTORY_SEPARATOR . str_replace(self::SEPARATOR, DIRECTORY_SEPARATOR, $key)
        . $this->getOption('suffix');
    }

    /**
     * Validate the path.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isValid($path)
    {
        $data = $this->read($path, self::READ_TIMEOUT);

        return time() < $data[self::READ_TIMEOUT];
    }

    /**
     * Reads the cache file and returns the content.
     *
     * @param string $path The file path
     * @param mixed $type The type of data you want to be returned
     *                     sfFileCache::READ_DATA: The cache content
     *                     sfFileCache::READ_TIMEOUT: The timeout
     *                     sfFileCache::READ_LAST_MODIFIED: The last modification timestamp
     *
     * @return array the (meta)data of the cache file. E.g. $data[sfFileCache::READ_DATA]
     *
     * @throws CacheException
     */
    protected function read($path, $type = self::READ_DATA)
    {
        if (!$fp = @fopen($path, 'rb')) {
            throw new CacheException(sprintf('Unable to read cache file "%s".', $path));
        }

        $data = [];
        @flock($fp, LOCK_SH);
        $data[self::READ_TIMEOUT] = intval(@stream_get_contents($fp, 12, 0));
        if ($type != self::READ_TIMEOUT && time() < $data[self::READ_TIMEOUT]) {
            if ($type & self::READ_LAST_MODIFIED) {
                $data[self::READ_LAST_MODIFIED] = intval(@stream_get_contents($fp, 12, 12));
            }
            if ($type & self::READ_DATA) {
                fseek($fp, 0, SEEK_END);
                $length = ftell($fp) - 24;
                fseek($fp, 24);
                $data[self::READ_DATA] = @fread($fp, $length);
            }
        } else {
            $data[self::READ_LAST_MODIFIED] = null;
            $data[self::READ_DATA] = null;
        }
        @flock($fp, LOCK_UN);
        @fclose($fp);

        return $data;
    }

    /**
     * Writes the given data in the cache file.
     *
     * @param string $path The file path
     * @param string $data The data to put in cache
     * @param int $timeout The timeout timestamp
     *
     * @return bool true if ok, otherwise false
     *
     * @throws CacheException
     */
    protected function write($path, $data, $timeout)
    {
        $current_umask = umask();
        umask(0000);

        if (!is_dir(dirname($path))) {
            // create directory structure if needed
            mkdir(dirname($path), 0777, true);
        }

        $tmpFile = tempnam(dirname($path), basename($path));

        if (!$fp = @fopen($tmpFile, 'wb')) {
            throw new CacheException(sprintf('Unable to write cache file "%s".', $tmpFile));
        }

        @fwrite($fp, str_pad($timeout, 12, 0, STR_PAD_LEFT));
        @fwrite($fp, str_pad(time(), 12, 0, STR_PAD_LEFT));
        @fwrite($fp, $data);
        @fclose($fp);

        rename($tmpFile, $path);

        chmod($path, 0666);

        umask($current_umask);

        return true;
    }
}
