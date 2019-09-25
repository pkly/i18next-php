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
        $res = $resolved['res'] ?? null;
        $resUsedKey = $resolved['usedKey'] ?? $key;
        $resExactUsedKey = $resolved['exactUsedKey'] ?? $key;

        $resType = gettype($res);
        $noObject = ['number', 'callable'];
        $joinArrays = $options['joinArrays'] ?? $this->_options['joinArrays'];

        $handleAsObjectInI18nFormat = $this->_i18nFormat->handleAsObject ?? false;
        $handleAsObject = !in_array($resType, $noObject);

        if ($handleAsObjectInI18nFormat && $res && $handleAsObject && !(is_string($joinArrays) && $resType === 'array')) {
            if (!($options['returnObjects'] ?? $this->_options['returnObjects'] ?? false)) {
                // TODO: warning accessing an object - but returnObjects options is not enabled!
                return isset($this->_options['returnedObjectHandler']) ?
                    call_user_func($this->_options['returnedObjectHandler'], $resUsedKey, $res, $options) :
                    'key ' . $key . ' (' . $this->_language . ') returned an object instead of string';
            }

            // if we got a separator we loop over children - else we just return object as is
            // as having it set to false means no hierarchy so no lookup for nested values
            if ($keySeparator) {
                // res type is required as array, so if somehow there'd be an object here it's required to implement the array interface
                $copy = [];

                $newKeyToUse = $resExactUsedKey;
                foreach ($res as $m => $v) {
                    $deepKey = $newKeyToUse.$keySeparator.$m;
                    $copy[$m] = $this->translate($deepKey, array_merge($options, ['joinArrays' => false, 'ns' => $namespaces]));

                    if ($copy[$m] === $deepKey)
                        $copy[$m] = $res[$m]; // if nothing found use original value as fallback
                }

                $res = $copy;
            }
        }
        else if ($handleAsObjectInI18nFormat && is_string($joinArrays) && $resType === 'array') {
            // array special treatment
            $res = array_merge($res, $joinArrays);
            if ($res)
                $res = $this->extendTranslation($res, $keys, $options);
        }
        else {
            // string empty or null
            $usedDefault = false;
            $usedKey = false;

            // fallback value
            if (!$this->isValidLookup($res) && isset($options['defaultValue'])) {
                $usedDefault = true;

                if (isset($options['count'])) {
                    $suffix = $this->_pluralResolver->getSuffix($lng, $options['count']);
                    $res = $options['defaultValue' . $suffix];
                }

                if (!$res)
                    $res = $options['defaultValue'];
            }

            if (!$this->isValidLookup($res)) {
                $usedKey = true;
                $res = $key;
            }

            // TODO: save missing ( https://github.com/i18next/i18next/blob/master/src/Translator.js#L176 )

            // extend
            $res = $this->extendTranslation($res, $keys, $options, $resolved);

            // append namespace if still key
            if ($usedKey && $res === $key && $this->_options['appendNamespaceToMissingKey'] ?? true)
                $res = $namespace . ':' . $key;

            // parseMissingKeyHandler
            if ($usedKey && isset($this->_options['parseMissingKeyHandler'])) {
                $res = call_user_func($this->_options['parseMissingKeyHandler'], $res);
            }
        }

        return $res;
    }

    public function extendTranslation($res, $key, $options, $resolved) {
        if (is_callable($this->_i18nFormat->parse ?? null)) {
            $res = $this->_i18nFormat->parse($res, $options, $resolved['usedLng'], $resolved['usedNS'], $resolved['usedKey'], $resolved);
        }
        else if (!($options['skipInterpolation'] ?? false)) {
            // i18next.parsing
            if ($options['interpolation'] ?? false)
                $this->_interpolator->init(array_merge($options, ['interpolation' => array_merge($this->_options['interpolation'] ?? [], $options['interpolation'] ?? [])]));

            $data = is_array($options['replace'] ?? null) ? $options['replace'] : $options;
            if (isset($this->_options['interpolation']['defaultVariables']))
                $data = array_merge($this->_options['interpolation']['defaultVariables'], $data);

            $res = $this->_interpolator->interpolate($res, $data, $options['lng'] ?? $this->_language, $options);

            // nesting
            if ($options['nest'] ?? true !== false)
                $res = $this->_interpolator->nest($res, function(...$args) { return $this->translate(...$args); }, $options);

            if ($options['interpolation'] ?? false)
                $this->_interpolator->reset();
        }

        // post process
        $postProcess = $options['postProcess'] ?? $this->_options['postProcess'] ?? [];
        $postProcessorNames = is_string($postProcess) ? [$postProcess] : $postProcess;

        if (!is_array($postProcessorNames))
            $postProcessorNames = [];

        if ($res && count($postProcessorNames) && $options['applyPostProcessor'] ?? true !== false)
            $res = PostProcessor::handle($postProcessorNames, $res, $key, $options, $this);

        return $res;
    }

    public function resolve($keys, array $options = []) {
        if (!is_array($keys))
            $keys = [$keys];

        $found = new \stdClass();
        $usedKey = null;
        $exactUsedKey = null;
        $usedLng = null;
        $usedNS = null;

        foreach ($keys as $key) {
            if ($this->isValidLookup($found))
                return;
        }
    }

    public function isValidLookup($res) {
        // In JS this includes an undefined check, but in PHP there's no such thing, so we're creating an stdClass instead, great I know.
        return !($res instanceof \stdClass) &&
            !($this->_options['returnNull'] ?? false && $res === null) &&
            !($this->_options['returnEmptyString'] ?? false && $res === '');
    }

    public function getResource($code, $ns, $key, array $options = []) {
        $f = $this->_i18nFormat->getResource ?? $this->_resourceStore->getResource;
        return $f($code, $ns, $key, $options);
    }
}