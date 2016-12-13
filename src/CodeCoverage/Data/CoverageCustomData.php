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

    /**
     * @param $data
     * @param $severity
     * @param string $key (clé de regroupement des erreurs
     */
    protected function addData($data, $severity, $key = null, $backtrace = null)
    {
        if ($backtrace === null) {
            $backtrace = $this->getBacktrace(2);
        }
        if (null == $key) {
            $key = json_encode($data);
        }
        $hash = md5($severity . $key);
        $hash_backtrace = md5($backtrace);
        if (isset($this->data[$hash])) {
            $this->data[$hash]["count"]++;
            if(isset($this->data[$hash]["backtrace"][$hash_backtrace])) {
                $this->data[$hash]["backtrace"][$hash_backtrace]["count"]++;
            }
        } else {
            $this->data[$hash] = array(
                "count" => 1,
                "severity" => $severity,
                "data" => $data,
                "backtrace" => [
                    $hash_backtrace => ["count"=>1,"trace"=>$backtrace]
                ]
            );
        }
    }

    protected function getBacktrace($ignoreCount = 1)
    {
        $stack = debug_backtrace();
        $ignoreCount++; // (pour ignorer ce level)
        while ($ignoreCount-- > 0 && !empty($stack)) {
            array_shift($stack);
        }
        $item = $stack[0];
        if (isset($item["class"])) {
            $function = $item["class"] . "::" . $item["function"];
        } else {
            $function = $item["function"];
        }

        $str_path = "";
        $str_fullpath = "";
        if (empty($stack)) {
            $str_path = $str_fullpath = $function;
        } else {
            while ($item = array_shift($stack)) {
                if (isset($item["class"])) {
                    $itm_fn = $item["class"] . "::<b>" . $item["function"] . "</b>";
                } else {
                    $itm_fn = "<b>" . $item["function"] . "</b>";
                }

                $str_fullpath .= $item["function"] . " < ";
                $str_path .= $item["function"] . " < ";
                $logStack[] = "$itm_fn (called at $item[file] - line $item[line])";
            }
        }

        $str_path = rtrim($str_path, " <");
        return $str_path;
//        echo implode("<br>", $logStack);
//        echo "<br>";
//        echo $str_path;
//        exit;
    }

}