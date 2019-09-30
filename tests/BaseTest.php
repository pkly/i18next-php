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
    public function testCIMode() {
        $i18n = new I18n([
            'lng'           =>  'cimode',
            'debug'         =>  true
        ]);

        echo $i18n->t('test');

        //$this->assertEquals('Hello world!', I18n::get()->t('key'));
    }
}