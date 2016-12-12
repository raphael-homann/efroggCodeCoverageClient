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

    public function getData()
    {
        return $this->data;
    }

    abstract public function getDataName();

    protected function addData($data)
    {
        d($data);
        $hash = md5(json_encode($data));
        if(isset($this->data[$hash])) {
            $this->data[$hash]["count"]++;
        } else {
            $this->data[$hash]=array(
                "count" => 1,
                "data" => $data
            );
        }
    }

}