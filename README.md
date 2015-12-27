# LockFile
php file util class

## Usage
```php
// lock
if (LockFile::lockExNg('/tmp/hoge.lock')) {
    // success
} else {
    // failure
}

// unlock
LockFile::unlock('/tmp/hoge.lock');
```

## Author
Masayuki Yoshii (Levely)

## License
MIT
