<?php
namespace Levely\LockFile;

/**
 * LockFile 
 * 
 * @author Masayuki Yoshii <masayuki_yoshii@icloud.com> 
 */
class LockFile
{
    /**
     * @var resource[] 
     */
    private static $handler = array();

    /**
     * lockExNb 
     * 
     * @param string $path file path
     * @static
     * @access public
     * @return boolean
     */
    public static function lockExNb($path)
    {
        $handler = static::getLockFile($path);
        if (empty($handler)) {
            return false;
        }
        if (flock($handler, LOCK_EX | LOCK_NB) === false) {
            return false;
        }
        static::setHandler($path, $handler);
        return true;
    }

    /**
     * unlock 
     * 
     * @param string $path file path
     * @static
     * @access public
     * @return boolean
     */
    public static function unlock($path)
    {
        $handler = static::getHandler($path);
        if (empty($handler)) {
            return false;
        }
        if (flock($handler, LOCK_UN) === false) {
            return false;
        }
        static::removeHandler($path);
        return true;
    }

    /**
     * getLockFile 
     * 
     * @param string $path 
     * @static
     * @access private
     * @return resource|null
     */
    private static function getLockFile($path)
    {
        $handler = @fopen($path, 'w+');
        if ($handler === false) {
            return null;
        }
        return $handler;
    }

    /**
     * setHandler 
     * 
     * @param string $path file path
     * @param resource $handler 
     * @static
     * @access private
     * @return void
     */
    private static function setHandler($path, $handler)
    {
        static::$handler[$path] = $handler;
    }

    /**
     * getHandler 
     * 
     * @param string $path file path
     * @static
     * @access private
     * @return resource
     */
    private static function getHandler($path)
    {
        return static::$handler[$path];
    }

    /**
     * removeHandler 
     * 
     * @param string $path file path
     * @static
     * @access private
     * @return void
     */
    private static function removeHandler($path)
    {
        unset(static::$handler[$path]);
    }
}
