<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 12/12/16
 * Time: 21:17
 */

namespace Efrogg\CodeCoverage\Data;


abstract class customData
{

    public function getData()
    {
        return array();
    }

    abstract public function getDataName();
}