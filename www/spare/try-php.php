<?php
/**
 * 暇つぶし
 * docker-compose exec www bash
 * php try-php.php
 */
echo 'hello'.PHP_EOL;

/**
 * プロパティ型指定
 */
class Hoge {
    public int $i = 0;
    public string $s = '';
    public ?object $obj;
}