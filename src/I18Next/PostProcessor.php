<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 25.09.2019
 * Time: 08:46
 */

namespace Pkly\I18Next;


class PostProcessor {
    private $_processors                        =   [];

    public function addPostProcessor(string $name, &$object) {
        $this->_processors[$name] = &$object;
    }

    public function handle($processors, $value, $key, array $options, $translator) {
        foreach ($processors as $name) {
            if (array_key_exists($name, $this->_processors))
                $value = $this->_processors[$name]->process($value, $key, $options, $translator);
        }

        return $value;
    }
}