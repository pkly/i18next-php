<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 14:47
 */

namespace Pkly\I18Next\Utils;

function getDefaults() {
    return [
        /* Logging */
        'debug'                                 =>  false,
        'initImmediate'                         =>  true,
        /* Languages, namespaces, resources */
        'ns'                                    =>  ['translation'],
        'defaultNS'                             =>  ['translation'],
        'fallbackLng'                           =>  ['en'],
        'fallbackNS'                            =>  false,

        'whitelist'                             =>  false,
        'nonExcplicitWhitelist'                 =>  false,
        'load'                                  =>  'all',
        'preload'                               =>  false,

        'simplifyPluralSuffix'                  =>  true,
        'keySeparator'                          =>  '.',
        'nsSeparator'                           =>  ':',
        'pluralSeparator'                       =>  '_',
        'contextSeparator'                      =>  '_',

        /* Missing keys */
        'partialBundledLanguages'               =>  false,
        'saveMissing'                           =>  false,
        'updateMissing'                         =>  false,
        'saveMissingTo'                         =>  'fallback',
        'saveMissingPlurals'                    =>  true,
        'missingKeyHandler'                     =>  false,
        'missingInterpolationHandler'           =>  false,

        /* Translation defaults */
        'postProcess'                           =>  false,
        'returnNull'                            =>  true,
        'returnEmptyString'                     =>  true,
        'returnObjects'                         =>  false,
        'joinArrays'                            =>  false,
        'returnObjectHandler'                   =>  null,
        'parseMissingKeyHandler'                =>  false,
        'appendNamespaceToMissingKey'           =>  false,
        'appendNamespaceToCIMode'               =>  false,
        'overloadTranslationOptionHandler'      =>  function (...$args) {
            $ret = [];

            if (is_array($args[1]))
                $ret = $args[1];
            if (is_string($args[1]))
                $ret['defaultValue'] = $args[1];
            if (is_string($args[2]))
                $ret['tDescription'] = $args[2];
            if (is_array($args[2]) || is_array($args[3])) {
                $options = $args[3] ?? $args[2];
                foreach ($options as $key => $option) {
                    $ret[$key] = $option;
                }
            }

            return $ret;
        },

        /* Interpolation */
        'interpolation'                         =>  [
            'escapeValue'                       =>  true,
            'format'                            =>  function ($value, $format, $lng) { return $value; },
            'prefix'                            =>  '{{',
            'suffix'                            =>  '}}',
            'formatSeparator'                   =>  ',',
            'unescapePrefix'                    =>  '-',

            'nestingPrefix'                     =>  '$t(',
            'nestingSuffix'                     =>  ')',
            'maxReplaces'                       =>  1000
        ]
    ];
}

function transformOptions(?array $options = null) {
    if (is_string($options['ns'] ?? null))
        $options['ns'] = [$options['ns']];

    if (is_string($options['fallbackLng'] ?? null))
        $options['fallbackLng'] = [$options['fallbackLng']];

    if (is_string($options['fallbackNS'] ?? null))
        $options['fallbackNS'] = [$options['fallbackNS']];

    return $options;
}

function noop(...$args) {}

function copy($search, &$from, &$to) {
    foreach ($search as $searchKey) {
        if (is_object($from)) {
            if (isset($from->{$searchKey})) {
                if (is_object($to))
                    $to->{$searchKey} = &$from->{$searchKey};
                else
                    $to[$searchKey] = &$from[$searchKey];
            }
        }
        else {
            if (isset($from[$searchKey])) {
                if (is_object($to))
                    $to->{$searchKey} = &$from[$searchKey];
                else
                    $to[$searchKey] = &$from[$searchKey];
            }
        }
    }
}

function getLastOfPath(&$object, $path, $Empty = null) {
    $cleanKey = function ($key) {
        return $key && mb_strpos($key, '###') !== false ? str_replace('###', '.', $key) : $key;
    };

    $obj = &$object;

    $canNotTraverseDeeper = function () use ($obj) {
        return $obj === null || is_string($obj);
    };

    $stack = is_array($path) ? $path : explode('.', $path);
    while (count($stack) > 1) {
        if ($canNotTraverseDeeper())
            return [];

        $key = $cleanKey(array_shift($stack));
        if (!isset($obj[$key]) && $Empty !== null) {
            $obj[$key] = $Empty;
        }

        $obj = &$obj[$key];
    }

    if ($canNotTraverseDeeper())
        return [];

    return [
        &$obj,
        $cleanKey(array_shift($stack))
    ];
}

function setPath(&$object, $path, $newValue) {
    list(&$obj, $key) = getLastOfPath($object, $path, []);

    $obj[$key] = $newValue;
}

function pushPath(&$object, $path, $newValue, bool $concat) {
    list(&$obj, $key) = getLastOfPath($object, $path, []);

    $obj[$key] = $obj[$key] ?? [];

    if ($concat)
        $obj[$key] = array_merge($obj[$key], $newValue);
    else
        $obj[$key][] = $newValue;
}

function getPath(&$object, $path) {
    list(&$obj, $key) = getLastOfPath($object, $path);

    if (!isset($obj))
        return null;

    return $obj[$key];
}

function deepMerge(array $target, array $source, bool $overwrite = false) {
    foreach ($source as $key => $value) {
        if (array_key_exists($key, $target)) {
            if (is_string($source[$key]) && is_string($target[$key])) {
                if ($overwrite)
                    $target[$key] = $source[$key];
            }
            else {
                $target = deepMerge($target[$key], $source[$key], $overwrite);
            }
        }
        else {
            $target[$key] = $source[$key];
        }
    }

    return $target;
}

function capitalize(string $str): string {
    return mb_strtoupper(mb_substr($str, 0, 1)).mb_substr($str, 1);
}

function regexEscape(string $str): string {
    return preg_replace("/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g", "\\$&", $str);
}

const ENTITY_MAP = [
    '&'         =>  '&amp;',
    '<'         =>  '&lt;',
    '>'         =>  '&gt;',
    '"'         =>  '&quot;',
    "'"         =>  '&#39;',
    '/'         =>  '&#x2F;'
];

function escape($str) {
    if (is_string($str)) {
        return str_replace(array_keys(ENTITY_MAP), ENTITY_MAP, $str);
    }

    return $str;
}