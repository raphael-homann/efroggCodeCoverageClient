<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 12/12/16
 * Time: 21:16
 */

namespace Efrogg\CodeCoverage\Data;


class CoverageErrorData extends CoverageCustomData
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
        return "error";
    }

    private function setErrorRepotingLevel($level)
    {
        $this->error_level = $level;
        set_error_handler(array($this,"handleError"),$level);
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
//        ob_start();
//        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//        $content = ob_get_clean();
        $this->addData(array(
            "errno" => $errno,
            "errfile" => $errfile,
            "errline" => $errline,
            "errstr" => $errstr
        ),$this->getSeverity($errno),$errfile.$errline);
    }

    private function getSeverity($errno)
    {
        switch($errno) {
            case E_WARNING:
            case E_STRICT:
            case E_USER_WARNING:
                return self::SEVERITY_WARNING;
            case E_NOTICE:
            case E_USER_NOTICE:
                return self::SEVERITY_NOTICE;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return self::SEVERITY_DANGER;
            case E_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_USER_ERROR:
                return self::SEVERITY_ERROR;
            default:
                return self::SEVERITY_NOTICE;
        }
    }

}