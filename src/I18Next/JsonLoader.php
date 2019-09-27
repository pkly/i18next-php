<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 27.09.2019
 * Time: 12:13
 */

namespace Pkly\I18Next;

class JsonLoader extends Loader {
    public function getModuleName(): string {
        return 'loader-basic-json';
    }
}