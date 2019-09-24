<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 14:45
 */

namespace I18Next;


class Translator {
    /**
     * @var array
     */
    private $_options                           =   [];

    /**
     * @var string
     */
    private $_language                          =   '';

    /**
     * @var ResourceStore|null
     */
    public $_resourceStore                      =   null;

    /**
     * @var LanguageUtil|null
     */
    public $_languageUtils                      =   null;

    /**
     * @var null
     * TODO: Add reference to PluralResolver after creation
     */
    public $_pluralResolver                     =   null;

    /**
     * @var Interpolator|null
     */
    public $_interpolator                       =   null;

    /**
     * @var null
     * TODO: Fix this as I've no idea what it is really
     */
    public $_i18nFormat                         =   null;

    public function __construct($services, array $options = []) {
        Utils\copy([
            '_resourceStore',
            '_languageUtils',
            '_pluralResolver',
            '_interpolator',
            '_backendConnector',
            '_i18nFormat'
        ], $services, $this);

        $this->_options = $options;
        if (!isset($this->_options['keySeparator'])) {
            $this->_options['keySeparator'] = '.';
        }

        // TODO: Create logger for component
    }

    public function changeLanguage(string $lng) {
        if ($lng)
            $this->_language = $lng;
    }

    public function exists(string $key, array $options = ['interpolation' => []]): bool {
        $resolved = $this->resolve($key, $options);
        // TODO: Check resolved use properly
        return $resolved && isset($resolved['res']);
    }

    public function extractFromKey(string $key, array $options = []) {
        $nsSeparator = $options['nsSeparator'] ?? $this->_options['nsSeparator'] ?? ':';
        $keySeparator = $options['keySeparator'] ?? $this->_options['keySeparator'];

        $namespaces = $options['ns'] ?? $this->_options['defaultNS'];

        if ($nsSeparator && mb_strpos($key, $nsSeparator) !== false) {
            $parts = explode($nsSeparator, $key);
            if ($nsSeparator !== $keySeparator || ($nsSeparator === $keySeparator && in_array($parts[0], $this->_options['ns'] ?? [])))
                $namespaces = array_shift($parts);

            $key = implode($keySeparator, $parts);
        }

        if (is_string($namespaces))
            $namespaces = [$namespaces];

        return [
            $key,
            $namespaces
        ];
    }

    public function translate($keys, ?array $options = null) {
        if (!is_array($options) && isset($this->_options['overloadTranslationOptionHandler'])) {
            $options = call_user_func($this->_options['overloadTranslationOptionHandler'], func_get_args());
        }

        if (!$options)
            $options = [];

        // non-valid key handling
        if (!$keys)
            return '';

        if (!is_array($keys))
            $keys = [$keys];

        // separators
        $keySeparator = $options['keySeparator'] ?? $this->_options['keySeparator'];

        // get namespaces
        list($key, $namespaces) = $this->extractFromKey($keys[count($keys) - 1], $options);
        $namespace = $namespaces[count($namespaces) - 1];

        // return key on CIMode
        $lng = $options['lng'] ?? $this->_language;
        $appendNamespaceToCIMode = $options['appendNamespaceToCIMode'] ?? $this->_options['appendNamespaceToCIMode'];
        if ($lng && mb_strtolower($lng) === 'cimode') {
            if ($appendNamespaceToCIMode) {
                $nsSeparator = $options['nsSeparator'] ?? $this->_options['nsSeparator'];
                return $namespace . $nsSeparator . $key;
            }

            return $key;
        }

        $resolved = $this->resolve($keys, $options);
        // TODO: Check resolved use properly
        $res = $resolved && isset($resolved['res']);
        $resUsedKey = $resolved['usedKey'] ?? $key;
        $resExactUsedKey = $resolved['exactUsedKey'] ?? $key;


    }

    public function getResource($code, $ns, $key, array $options = []) {
        $f = $this->_i18nFormat->getResource ?? $this->_resourceStore->getResource;
        return $f($code, $ns, $key, $options);
    }
}