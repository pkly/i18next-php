<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 13:06
 */

namespace I18Next;

require_once __DIR__ . '/Utils.php';

const RTL_LANGUAGES = [
    'ar', 'shu', 'sqr', 'ssh', 'xaa', 'yhd', 'yud', 'aao', 'abh', 'abv', 'acm', 'acq',
    'acw', 'acx', 'acy', 'adf', 'ads', 'aeb', 'aec', 'afb', 'ajp', 'apc', 'apd', 'arb',
    'arq', 'ars', 'ary', 'arz', 'auz', 'avl', 'ayh', 'ayl', 'ayn', 'ayp', 'bbz', 'pga',
    'he', 'iw', 'ps', 'pbt', 'pbu', 'pst', 'prp', 'prd', 'ur', 'ydd', 'yds', 'yih', 'ji',
    'yi', 'hbo', 'men', 'xmn', 'fa', 'jpr', 'peo', 'pes', 'prs', 'dv', 'sam',
];

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
     * @var null|\stdClass
     */
    private $_services                          =   null;

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