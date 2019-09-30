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

    public static function getDefaults(): array {
        return [
            'parse'             =>  '\json_decode',
            'json_root_path'    =>  ''
        ];
    }

    public function getModuleName(): string {
        return 'loader-basic-json';
    }

    public function init(&$services, array $options, I18n &$instance): void {
        parent::init($services, $options, $instance);
        $this->_options = array_merge_recursive($this->_options, self::getDefaults());

        if (!isset($options['json_resource_path'])) {
            $this->_logger->error('No resource path was found for the JsonLoader instance', $options);
            return;
        }

        $this->_filePath = $options['json_resource_path'];
    }

    public function read($language, $namespace) {
        /**
         * @var Interpolator $interpolator
         */
        $interpolator = &$this->_services->_interpolator;
        $path = $interpolator->interpolate($this->_filePath, ['lng' => $language, 'ns' => $namespace]);

        return $this->load($path);
    }

    private function load($path) {
        $paths = [$path, $this->_options['json_root_path'] . $path];
        $path = null;

        foreach ($paths as $p) {
            if (is_file($p)) {
                $path = $p;
                break;
            }
        }

        if (!$path) {
            $this->_logger->error('Json target file not found', $paths);
            return null;
        }

        try {
            $data = file_get_contents($path);
            $ret = call_user_func($this->_options['parse'], $data);

            if ($this->_options['parse'] === self::getDefaults()['parse']) {
                if (json_last_error() !== JSON_ERROR_NONE)
                    throw new \Exception('Json parse error');
            }

            return $ret;
        }
        catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), $path);
            return null;
        }
    }
}