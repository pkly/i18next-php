<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 14:04
 */

namespace I18Next;


class ResourceStore {
    private $_data                              =   [];
    private $_options                           =   [];

    public function __construct(array $data = [], ?array $options = null) {
        $this->_data = $data;

        $defaults = Utils\getDefaults();

        if ($options === null) {
            $options = [
                'ns'            =>  $defaults['ns'],
                'defaultNS'     =>  $defaults['defaultNS']
            ];
        }

        if (!isset($options['keySeparator'])) {
            $options['keySeparator'] = $defaults['keySeparator'];
        }

        $this->_options = $options;
    }

    public function addNamespaces($namespaces) {
        if (!is_array($namespaces))
            $namespaces = [$namespaces];

        foreach ($namespaces as $namespace)
            if (!in_array($namespace, $this->_options['ns']))
                $this->_options['ns'][] = $namespace;
    }

    public function removeNamespaces($namespaces) {
        if (!is_array($namespaces))
            $namespaces = [$namespaces];

        foreach ($namespaces as $namespace) {
            $key = array_search($namespace, $this->_options['ns']);
            if ($key !== false)
                unset($this->_options['ns'][$key]);
        }

        // Technically not required but otherwise the keys will be a mess when removing namespaces
        $this->_options['ns'] = array_values($this->_options['ns']);
    }

    public function getResource(string $lng, string $ns, $key, array $options = []) {
        $keySeparator = isset($options['keySeparator']) ? $options['keySeparator'] : $this->_options['keySeparator'];

        $path = [$lng, $ns];

        if ($key && !is_string($key))
            $path = array_merge($path, $key);

        if ($key && is_string($key))
            $path = array_merge($path, ($keySeparator ? explode($keySeparator, $key) : $key));

        if (mb_strpos($lng, '.') !== false) {
            $path = explode($lng, '.');
        }

        return Utils\getPath($this->_data, $path);
    }
}