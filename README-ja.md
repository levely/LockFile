# LockFile

## はじめに
簡単な排他処理が欲しくて、ファイルロック機能を使った単純なクラスを作ってみました。ロック中に他のプロセスがロックをしようとすると即時にfalseを返します。

## Usage
```php
// ロックを試みる
if (LockFile::lockExNg('/tmp/hoge.lock')) {
    // ロック成功
} else {
    // ロック失敗
}

// ロックを開放する
LockFile::unlock('/tmp/hoge.lock');
```

## Author
Masayuki Yoshii (Levely)

