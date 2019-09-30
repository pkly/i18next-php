<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 27.09.2019
 * Time: 12:13
 */

namespace Pkly\I18Next;

use Psr\Log\LoggerInterface;

require_once __DIR__ . '/Utils.php';

class Loader implements ModuleInterface {
    /**
     * @var array
     */
    protected $_options                         =   [];

    /**
     * @var LoggerInterface|null
     */
    protected $_logger                          =   null;

    /**
     * @var I18n|null
     */
    protected $_i18n                            =   null;

    /**
     * @var \stdClass|null
     */
    protected $_services                        =   null;

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
        $this->_options = array_merge_recursive($options, Utils\getDefaults());
        $this->_i18n = &$instance;
        $this->_logger = &$services->_logger;
        $this->_services = &$services;
    }

    public function create($languages, $namespace, $key, $fallbackValue, array $options = [], $isUpdate = false) {}

    public function read($language, $namespace) {}
}