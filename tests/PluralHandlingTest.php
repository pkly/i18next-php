<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 04.10.2019
 * Time: 08:49
 */

namespace Pkly\I18Next\Tests;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Pkly\I18Next\I18n;

class PluralHandlingTest extends TestCase {
    public function testBasic() {
        $log = new Logger('TestLogger');
        $log->pushHandler(new StreamHandler('php://stdout'));

        $i18n = new I18n([
            'lng'               =>  'en',
            'debug'             =>  true,
            'resources'         =>  [
                'en'            =>  [
                    'translation'       =>  [
                        'key'                   =>  'Item',
                        'key_plural'            =>  'Items',
                        'kWithCount'            =>  '{{count}} item',
                        'kWithCount_plural'     =>  '{{count}} items'
                    ]
                ]
            ]
        ], $log);

        $this->assertEquals('Items', $i18n->t('key', ['count' => 0]));
        $this->assertEquals('Items', $i18n->t('key', ['count' => 1]));
        $this->assertEquals('Items', $i18n->t('key', ['count' => 4]));
    }
}