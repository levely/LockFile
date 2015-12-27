<?php
namespace Levely\Test\LockFile;

use Levely\LockFile\LockFile;

/**
 * LockFileTest 
 * 
 * @author Masayuki Yoshii <masayuki_yoshii@icloud.com> 
 */
class LockFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test lock file path 
     */
    private static $tmpLockFilePath = '/tmp/test.lock';
    
    /**
     * tearDown 
     * 
     * @access public
     * @return void
     */
    public function tearDown()
    {
        if (file_exists(static::$tmpLockFilePath)) {
            unlink(static::$tmpLockFilePath);
        }
        $this->assertFalse(file_exists(static::$tmpLockFilePath));
    }

    /**
     * testExLock
     * 
     * @access public
     * @return void
     */
    public function testExLock()
    {
        // test
        $actualLockSuccess = LockFile::lockExNb(static::$tmpLockFilePath);
        $actualLockFailure = LockFile::lockExNb(static::$tmpLockFilePath);
        flock(static::closureGetHandler(static::$tmpLockFilePath), LOCK_UN);
        $actualReLockSuccess = LockFile::lockExNb(static::$tmpLockFilePath);

        // assert
        $this->assertTrue($actualLockSuccess);
        $this->assertFalse($actualLockFailure);
        $this->assertTrue($actualReLockSuccess);

        // tear down
        flock(static::closureGetHandler(static::$tmpLockFilePath), LOCK_UN);
    }

    /**
     * testUnLock 
     * 
     * @access public
     * @return void
     */
    public function testUnLock()
    {
        // test
        $handler = @fopen(static::$tmpLockFilePath, 'w+');
        $locked = flock($handler, LOCK_EX | LOCK_NB);
        $actualUnlocked = LockFile::unlock(static::$tmpLockFilePath);
        $actualUnlockFailure = LockFile::unlock('/tmp/test2.lock');

        // actual
        $this->assertSame('resource', gettype($handler));
        $this->assertTrue($locked);
        $this->assertTrue($actualUnlocked);
        $this->assertFalse($actualUnlockFailure);
    }

    /**
     * testGetLockFile 
     * 
     * @access public
     * @return void
     */
    public function testGetLockFile()
    {
        // test
        $getLockFile = \Closure::bind(function($path) {
            return static::getLockFile($path);
        }, null, LockFile::class);
        $actualSuccess = $getLockFile(static::$tmpLockFilePath);
        $actualFailure = $getLockFile('test://hogehoge');

        // assert
        $this->assertSame('resource', gettype($actualSuccess));
        $this->assertNull($actualFailure);
    }

    /**
     * testSetHander 
     * 
     * @access public
     * @return void
     */
    public function testSetHander()
    {
        // test
        $setHandler = \Closure::bind(function($path, $handler) {
            static::setHandler($path, $handler);
        }, null, LockFile::class);
        $setHandler('aa', 'aaa');

        // assert
        $this->assertSame('aaa', static::closureGetHandler('aa'));
    }

    /**
     * testGetHander 
     * 
     * @access public
     * @return void
     */
    public function testGetHander()
    {
        //test
        static::closureSetHandler('a', 'aa');
        $getHandler = \Closure::bind(function($path) {
            return static::getHandler($path);
        }, null, LockFile::class);

        // assert
        $this->assertSame('aa', $getHandler('a'));
    }

    /**
     * testRemoveHander 
     * 
     * @access public
     * @return void
     */
    public function testRemoveHander()
    {
        // test
        static::closureSetHandler(static::$tmpLockFilePath, 'a');
        $actualConfirm = static::closureGetHandler(static::$tmpLockFilePath);
        static::closureRemoveHandler(static::$tmpLockFilePath);
        $actualRemoved = static::closureGetHandler(static::$tmpLockFilePath);
        
        // assert 
        $this->assertSame('a', $actualConfirm);
        $this->assertNull($actualRemoved);
    }
    
    /**
     * closureSetHandler 
     * 
     * @param mixed $path 
     * @param mixed $handler 
     * @static
     * @access private
     * @return void
     */
    private static function closureSetHandler($path, $handler)
    {
        \Closure::bind(function() use ($path, $handler) {
            static::$handler[$path] = $handler;
        }, null, LockFile::class)->__invoke();
    }

    /**
     * closureGetHandler 
     * 
     * @param mixed $path 
     * @static
     * @access private
     * @return void
     */
    private static function closureGetHandler($path)
    {
        return \Closure::bind(function() use ($path) {
            return static::$handler[$path];
        }, null, LockFile::class)->__invoke();
    }

    /**
     * closureRemoveHandler 
     * 
     * @param mixed $path 
     * @static
     * @access private
     * @return void
     */
    private static function closureRemoveHandler($path)
    {
        return \Closure::bind(function() use ($path) {
            static::removeHandler($path);
        }, null, LockFile::class)->__invoke();
    }
}
