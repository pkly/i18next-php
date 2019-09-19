<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 13:06
 */

namespace I18Next;

require_once __DIR__ . '/Utils.php';

use I18Next\Exception\Uninitialized;

class I18n {
    /**
     * A global instance of I18next
     * @var null|I18Instance
     */
    private static $_instance                   =   null;

    /**
     * Initialize I18Next globally
     *
     * @param array|null $options
     * @param bool $registerAsInstance
     */
    public static function init(?array $options = null) {
        self::$_instance = self::createInstance($options);
    }

    /**
     * Create an instance
     *
     * @param array|null $options
     * @return I18Instance
     */
    public static function createInstance(?array $options = null): I18Instance {
        if ($options === null)
            $options = Utils\getDefaults();

        return new I18Instance($options);

        // Test
    }

    /**
     * Clone an instance
     *
     * @param array|null $options
     * @return I18Instance
     * @throws Uninitialized
     */
    public static function cloneInstance(?array $options = null): I18Instance {
        if (self::$_instance === null)
            throw new Uninitialized();

        // TODO: implement this
    }

    /**
     * Call forwarder for the instance
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Uninitialized
     */
    public static function __callStatic($name, $arguments) {
        if (self::$_instance === null)
            throw new Uninitialized();

        return self::$_instance->{$name}(...$arguments);
    }
}