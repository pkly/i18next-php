<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 20.09.2019
 * Time: 08:32
 */

namespace I18Next;


class LanguageUtil {
    private $_options                           =   [];
    private $_whitelist                         =   false;

    public function __construct(array $options = []) {
        $this->_options = $options;

        $this->_whitelist = $this->_options['whitelist'] ?? false;
    }

    public function getScriptPartFromCode(string $code) {
        if (!$code || mb_strpos($code, '-') === false)
            return null;

        $p = explode('-', $code);
        if (count($p))
            return null;
        array_pop($p);
        // format language code
    }

    public function getLanguagePartFromCode(string $code) {
        if (!$code || mb_strpos($code, '-') === false)
            return $code;

        $p = explode('-', $code);
        // format language code
    }

    public function formatLanguageCode(string $code) {
        if (mb_strpos($code, '-') !== false) {
            $specialCases = ['hans', 'hant', 'latn', 'cyrl', 'cans', 'mong', 'arab'];
            $p = explode('-', $code);

            if ($this->_options['lowerCaseLng'] ?? false) {
                $p = array_map(function($o) {
                    return mb_strtolower($o);
                }, $p);
            }
            else if (count($p) === 2) {
                $p[0] = mb_strtolower($p[0]);
                $p[1] = mb_strtolower($p[1]);
            }
        }

        return implode('-', $code);
    }
}