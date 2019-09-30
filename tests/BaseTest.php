<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 27.09.2019
 * Time: 14:15
 */

namespace Pkly\I18Next\Tests;

use PHPUnit\Framework\TestCase;
use Pkly\I18Next\I18n;

class BaseTest extends TestCase {
    public function testBasic() {
        I18n::get([
            'lng'           =>  'en',
            'debug'         =>  true,
            'resources'     =>  [
                'en'        =>  [
                    'translation'       =>  [
                        'key'           =>  'Hello world!'
                    ]
                ]
            ]
        ]);

        $this->assertEquals('Hello world!', I18n::get()->t('key'));
    }
}