<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 27.09.2019
 * Time: 12:13
 */

namespace Pkly\I18Next;

class Loader implements ModuleInterface {
    /**
     * @inheritDoc
     */
    public function getModuleType(): string {
        return MODULE_TYPE_LOADER;
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string {
        return 'loader-base';
    }

    /**
     * @inheritDoc
     */
    public function init(&$services, array $options, I18n &$instance): void {
        return;
    }

    public function create($languages, $namespace, $key, $fallbackValue, array $options = [], $isUpdate = false) {

    }

    public function read($language, $namespace) {

    }
}