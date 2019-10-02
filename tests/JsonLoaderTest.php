<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 01.10.2019
 * Time: 12:18
 */

namespace Pkly\I18Next\Tests;


use PHPUnit\Framework\TestCase;
use Pkly\I18Next\I18n;
use Pkly\I18Next\JsonLoader;

class JsonLoaderTest extends TestCase {
    public function testLoading() {
        $jsonLoader = new JsonLoader();

        $i18n = new I18n([
            'lng'                   =>  'en',
            'debug'                 =>  true,
            'json_resource_path'    =>  __DIR__ . '/data/{{lng}}/{{ns}}.json'
        ]);

        $i18n->useModule($jsonLoader);

        $this->assertEquals('value from json!', $i18n->t('key'));
    }
}