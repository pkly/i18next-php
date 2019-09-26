<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 13:06
 */

namespace Pkly\I18Next;

require_once __DIR__ . '/Utils.php';

const RTL_LANGUAGES = [
    'ar', 'shu', 'sqr', 'ssh', 'xaa', 'yhd', 'yud', 'aao', 'abh', 'abv', 'acm', 'acq',
    'acw', 'acx', 'acy', 'adf', 'ads', 'aeb', 'aec', 'afb', 'ajp', 'apc', 'apd', 'arb',
    'arq', 'ars', 'ary', 'arz', 'auz', 'avl', 'ayh', 'ayl', 'ayn', 'ayp', 'bbz', 'pga',
    'he', 'iw', 'ps', 'pbt', 'pbu', 'pst', 'prp', 'prd', 'ur', 'ydd', 'yds', 'yih', 'ji',
    'yi', 'hbo', 'men', 'xmn', 'fa', 'jpr', 'peo', 'pes', 'prs', 'dv', 'sam',
];

const STORE_API = [
    'getResource',
    'addResource',
    'addResources',
    'addResourceBundle',
    'removeResourceBundle',
    'hasResourceBundle',
    'getResourceBundle',
    'getDataByLanguage'
];

/**
 * Class I18n
 *
 * Base class for translating text
 *
 * To use either create a new instance yourself, or simply initialize it globally via I18n::get() (suggested)
 *
 * @package Pkly\I18Next
 */
class I18n {
    /**
     * @var array
     */
    private $_options                           =   [];

    /**
     * @var array
     */
    private $_languages                         =   [];

    /**
     * @var string
     */
    private $_language                          =   '';

    /**
     * @var array
     */
    private $_modules                           =   ['external' => []];

    /**
     * @var null|\Closure
     */
    private $_format                            =   null;

    /**
     * @var null|\stdClass
     */
    private $_services                          =   null;

    /**
     * @var ResourceStore|null
     */
    private $_store                             =   null;

    /**
     * @var Translator|null
     */
    private $_translator                        =   null;

    /**
     * @var null|I18n
     */
    private static $_instance                   =   null;

    /**
     * @return I18n
     */
    public static function get(): I18n {
        if (self::$_instance === null)
            self::$_instance = new I18n();

        return self::$_instance;
    }

    public function __construct(array $options = []) {
        $this->_options = Utils\transformOptions($options);

        $this->_services = new \stdClass();

        $this->init($this->_options);
    }

    public function init(array $options = []) {
        $this->_options = array_merge_recursive(Utils\getDefaults(), $this->_options, Utils\transformOptions($options));

        $this->_format = $this->_options['interpolation']['format'];

        // init services
        if (!$this->_options['isClone']) {
            // TODO: Create logger

            $this->_store = new ResourceStore($this->_options['resources'] ?? [], $this->_options);

            // TODO: Add logged to services as _logger
            $this->_services->_resourceStore = &$this->_store;
            $this->_services->_languageUtils = new LanguageUtil($this->_options);
            $this->_services->_pluralResolver = new PluralResolver($this->_services->_languageUtils, [
                'prepend'               =>  $this->_options['pluralSeparator'],
                'compatibilityJSON'     =>  $this->_options['compatibilityJSON'],
                'simplifyPluralSuffix'  =>  $this->_options['simplifyPluralSuffix']
            ]);
            $this->_services->_interpolator = new Interpolator($this->_options);

            // TODO: look over the module loading code from ( https://github.com/i18next/i18next/blob/master/src/i18next.js#L86 )

            $this->_translator = new Translator($this->_services, $this->_options);

            // TODO: Possibly init modules?

            // append api
            foreach (STORE_API as $fcName) {
                unset($this->{$fcName});
                $this->{$fcName} = function (...$args) use ($fcName) {
                    return call_user_func([$this->_store, $fcName], ...$args);
                };
            }
        }
    }

    public function dir(?string $lng): string {
        if (!$lng)
            $lng = $this->_languages[0] ?? $this->_language;

        if (!$lng)
            return 'rtl';

        return in_array($this->_services->_languageUtils->getLanguagePartFromCode($lng), RTL_LANGUAGES) ? 'rtl' : 'ltr';
    }

    public function __clone() {
        $clone = clone $this;
        $clone->_options = array_merge($clone->_options, ['isClone' => true]);
        return $clone;
    }
}