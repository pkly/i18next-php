<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 27.09.2019
 * Time: 10:37
 */

namespace Pkly\I18Next;


use Psr\Log\LoggerInterface;

class Loader {
    /**
     * @var array
     */
    private $_options                           =   [];

    /**
     * @var ResourceStore|null
     */
    private $_store                             =   null;

    /**
     * @var LanguageUtil|null
     */
    private $_languageUtils                     =   null;

    /**
     * @var LoggerInterface|null
     */
    private $_logger                            =   null;

    public function __construct(ResourceStore &$store, &$services, array $options = []) {
        $this->_store = &$store;
        $this->_options = $options;
        $this->_languageUtils = &$services->_languageUtils;
        $this->_logger = &$services->_logger;
    }

    public function read($lng, $ns, $fcName, $tried = 0) {
        if (!$lng)
            return;


    }

    public function prepareLoading($languages, $namespaces, array $options = []) {

    }

    public function load($languages, $namespaces) {
        $this->prepareLoading($languages, $namespaces);
    }

    public function reload($languages, $namespaces) {
        $this->prepareLoading($languages, $namespaces, ['reload' => true]);
    }

    public function loadOne($name, string $prefix = '') {
        list($lng, $ns) = explode("|", $name);


    }
}