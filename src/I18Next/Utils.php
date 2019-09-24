<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 14:47
 */

namespace I18Next\Utils;

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
        'overloadTranslationOptionHandler'      =>  null,

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
        'obj'       =>  &$obj,
        'k'         =>  $cleanKey(array_shift($stack))
    ];
}

function setPath(&$object, $path, $newValue) {
    $res = getLastOfPath($object, $path, []);

    $res['obj'][$res['k']] = $newValue;
}

function pushPath(&$object, $path, $newValue, bool $concat) {
    $res = getLastOfPath($object, $path, []);

    $res['obj'][$res['k']] = $res['obj'][$res['k']] ?? [];

    if ($concat)
        $res['obj'][$res['k']] = array_merge($res['obj'][$res['k']], $newValue);
    else
        $res['obj'][$res['k']][] = $newValue;
}

function getPath(&$object, $path) {
    $res = getLastOfPath($object, $path);

    if (!isset($res['obj']))
        return null;

    return $res['obj'][$res['k']];
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