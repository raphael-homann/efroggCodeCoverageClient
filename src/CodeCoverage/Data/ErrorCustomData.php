<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 12/12/16
 * Time: 21:16
 */

namespace Efrogg\CodeCoverage\Data;


class ErrorCustomData extends customData
{

    protected $error_level = E_ALL ^ E_NOTICE;
    /**
     * ErrorCustomData constructor.
     * @param int $level
     */
    public function __construct($level = E_ALL ^ E_NOTICE)
    {
        $this->setErrorRepotingLevel($level);

    }

    public function getDataName()
    {
        return "errors";
    }

    private function setErrorRepotingLevel($level)
    {
        $this->error_level = $level;
        set_error_handler(array($this,"handleError"),$level);
    }

    protected function handleError($error) {
        d($error);
    }
}