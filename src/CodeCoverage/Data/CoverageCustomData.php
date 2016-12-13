<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 12/12/16
 * Time: 21:17
 */

namespace Efrogg\CodeCoverage\Data;


abstract class CoverageCustomData
{

    protected $data = array();

    const SEVERITY_NOTICE = 1;
    const SEVERITY_WARNING = 2;
    const SEVERITY_DANGER = 3;
    const SEVERITY_ERROR = 4;

    public function getData()
    {
        return $this->data;
    }

    abstract public function getDataName();

    protected function addData($data,$severity,$key)
    {
        $hash = md5($severity.$key);
        if(isset($this->data[$hash])) {
            $this->data[$hash]["count"]++;
        } else {
            $this->data[$hash]=array(
                "count" => 1,
                "severity" => $severity,
                "data" => $data
            );
        }
    }

}