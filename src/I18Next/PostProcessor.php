<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 25.09.2019
 * Time: 08:46
 */

namespace I18Next;


abstract class PostProcessor {
    protected static $_processors               =   [];

    public static function addPostProcessor(string $name, callable $callable) {
        self::$_processors[$name] = $callable;
    }

    public static function handle($processors, $value, $key, array $options, $translator) {
        foreach ($processors as $name) {
            if (array_key_exists($name, self::$_processors))
                $value = self::$_processors[$name]->process($value, $key, $options, $translator);
        }

        return $value;
    }
}