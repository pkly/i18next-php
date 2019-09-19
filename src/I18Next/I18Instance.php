<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 18.09.2019
 * Time: 13:49
 */

namespace I18Next;


class I18Instance {
    /**
     * Options for the current instance
     *
     * For full list of options see https://www.i18next.com/overview/configuration-options
     * @var null|array
     */
    private $_options                           =   null;

    /**
     * List of languages that were actively requested, in order of importance
     *
     * @var string[]
     */
    private $_languages                         =   [];

    /**
     * List of fallbacks that might be loaded if required, in order of importance
     *
     * @var string[]
     */
    private $_fallback                          =   [];

    /**
     * List of bundles
     *
     * @var ResourceStore[]
     */
    private $_bundles                           =   [];

    /**
     * I18Instance constructor.
     *
     * @param array $options
     */
    public function __construct(array $options) {
        $this->_options = array_merge(I18n::DEFAULT_OPTIONS, $options);
    }

    /**
     * @param $keys
     * @param array|null $options
     * @return string|null
     */
    public function t($keys, ?array $options): ?string {
        if (!is_array($keys))
            $keys = [$keys];

        foreach ($this->_bundles as $bundle) {
            foreach ($keys as $key) {

            }
        }
    }
}