<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 14:04
 */

namespace I18Next;


class ResourceStore {
    private $_storage                           =   [];

    public function add(string $lang, string $namespace, array $resources = [], bool $deep = false, bool $overwrite = false) {
        if (!is_array($this->_storage[$lang] ?? null))
            $this->_storage[$lang] = [];

        if (!is_array($this->_storage[$lang][$namespace] ?? null))
            $this->_storage[$lang][$namespace] = [];

        // TODO: differentiate between deep/overwrite
        $this->_storage[$lang][$namespace] = $resources;
    }

    public function has(string $lang, string $namespace): bool {
        return is_array($this->_storage[$lang][$namespace] ?? null);
    }

    public function get(string $lang, string $namespace): ?array {
        return $this->_storage[$lang][$namespace] ?? null;
    }

    public function remove(string $lang, string $namespace) {
        if ($this->has($lang, $namespace))
            unset($this->_storage[$lang][$namespace]);
    }

    public function getByLanguage(string $lang): ?array {
        return $this->_storage[$lang] ?? null;
    }
}