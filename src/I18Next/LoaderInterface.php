<?php
/**
 * Created by PhpStorm.
 * User: pkly
 * Date: 27.09.2019
 * Time: 09:15
 */

namespace Pkly\I18Next;


interface LoaderInterface {
    public function setResourceStore(ResourceStore &$store);

    public function load(array $toLoad, $ns);
}