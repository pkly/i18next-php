<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 14:04
 */

namespace I18Next;


class ResourceStore implements \JsonSerializable {
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

        $options['keySeparator'] = $options['keySeparator'] ?? $defaults['keySeparator'];

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

    public function getResource(string $lng, string $ns, $key = null, array $options = []) {
        $keySeparator = $options['keySeparator'] ?? $this->_options['keySeparator'];

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

    public function addResource(string $lng, $ns, $key, $value, array $options = ['silent' => false]) {
        $keySeparator = $options['keySeparator'] ?? $this->_options['keySeparator'];

        $path = [$lng, $ns];

        if ($key)
            $path = array_merge($path, ($keySeparator ? explode($keySeparator, $key) : $key));

        if (mb_strpos($lng, '.') !== false) {
            $path = explode('.', $lng);
            $value = $ns;
            $ns = $path[1];
        }

        $this->addNamespaces($ns);

        Utils\setPath($this->_data, $path, $value);

        // event emitting here
    }

    public function addResources(string $lng, $ns, array $resources, array $options = ['silent' => false]) {
        foreach ($resources as $key => $value) {
            if (is_string($value))
                $this->addResource($lng, $ns, $key, $value, $options);
        }

        // event emitting here
    }

    public function addResourceBundle(string $lng, $ns, $resources, bool $deep = false, bool $overwrite = false, array $options = ['silent' => false]) {
        $path = [$lng, $ns];

        if (mb_strpos($lng, '.') !== false) {
            $path = explode('.', $lng);
            $deep = $resources;
            $resources = $ns;
            $ns = $path[1];
        }

        $this->addNamespaces($ns);

        $pack = Utils\getPath($this->_data, $path) ?? [];

        if ($deep) {
            $pack = Utils\deepMerge($pack, $resources, $overwrite);
        }
        else {
            $pack = array_merge($pack, $resources);
        }

        Utils\setPath($this->_data, $path, $pack);

        // event emitting here
    }

    public function removeResourceBundle(string $lng, string $ns) {
        if ($this->hasResourceBundle($lng, $ns)) {
            unset($this->_data[$lng][$ns]);
        }

        $this->removeNamespaces($ns);

        // event emitting here
    }

    public function hasResourceBundle(string $lng, string $ns): bool {
        return $this->getResource($lng, $ns) !== null;
    }

    public function getResourceBundle(string $lng, ?string $ns = null) {
        if ($ns === null)
            $ns = $this->_options['defaultNS'];

        return $this->getResource($lng, $ns);
    }

    public function getDataByLanguage(string $lng) {
        return $this->_data[$lng] ?? null;
    }

    public function jsonSerialize() {
        return $this->_data ?? [];
    }
}