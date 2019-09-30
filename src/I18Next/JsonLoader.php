<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 27.09.2019
 * Time: 12:13
 */

namespace Pkly\I18Next;

class JsonLoader extends Loader {
    /**
     * @var string|null
     */
    private $_filePath                          =   null;

    public function getModuleName(): string {
        return 'loader-basic-json';
    }

    public function init(&$services, array $options, I18n &$instance): void {
        parent::init($services, $options, $instance);

        if (!isset($options['json_resource_path'])) {
            $this->_logger->error('No resource path was found for the JsonLoader instance', $options);
            return;
        }

        $this->_filePath = $options['json_resource_path'];
    }
}