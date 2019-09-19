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