<?php

/**
 * Created by PhpStorm.
 * User: raph
 * Date: 03/06/16
 * Time: 17:22
 */
namespace Efrogg\CodeCoverage;

use Efrogg\CodeCoverage\Data\CoverageCustomData;
use Efrogg\CodeCoverage\Persister\PersisterInterface;

class CodeCoverageClient
{
    protected $projectName = 'default';
    protected $sessionId = null;
    protected $responseData = null;
    protected $initial_time;

    protected $coverage_active = true;

    /** @var CoverageApiServer */
    private $apiServer = null;
    private $coverageIsRunning = false;


    /** @var CoverageCustomData[] */
    protected $data_handlers = array();

    protected $rootPath = "";

    protected $verbose = 0;

    /**
     * CodeCoverageClient constructor.
     * @param CoverageApiServer $apiServer
     */
    public function __construct(CoverageApiServer $apiServer)
    {
        $this->apiServer = $apiServer;
        $this->apiServer->setProjectName($this->projectName);

    }

    /** @var CodeCoverageClient */
    protected static $instance = null;

    /**
     * @param CodeCoverageClient $instance
     */
    public static function setInstance(CodeCoverageClient $instance)
    {
        self::$instance = $instance;
    }

    /**
     * @return CodeCoverageClient
     */
    public static function getInstance()
    {
        return self::$instance;
    }


    public function beginCoverage()
    {
        $this->initial_time = microtime(true);

        if ($this->coverageIsAvailable()) {
            if ($this->coverage_active) {
                xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
//            xdebug_start_code_coverage();
            }
            $this->coverageIsRunning = true;
            $self = $this;
            register_shutdown_function(function () use ($self) {
                $self->commitCoverage();
            });

        }

    }

    public function commitCoverage()
    {
        if ($this->coverageIsRunning) {
            if (is_null($this->apiServer)) {
                throw new \Exception("Coverage Api server not configured");
            }
            $commit_time = microtime(true);

            $coverageData = array("coverage" => array(), "custom" => array());
            if ($this->coverage_active) {
                if (!empty($this->rootPath)) {
                    $root = realpath($this->rootPath);
                    $rootLen = strlen($root);
                    foreach (xdebug_get_code_coverage() as $file => $lines) {
                        if (strpos($file, $root) === 0) {
                            $file = substr($file, $rootLen);
                        }
                        $coverageData["coverage"][$file] = $lines;
                    }
                } else {
                    $coverageData["coverage"] = xdebug_get_code_coverage();
                }
            }
            foreach ($this->data_handlers as $handler) {
                $coverageData["custom"][$handler->getDataName()] = $handler->getData();
            }
            $coverageData["url"] = $_SERVER["REQUEST_URI"];
            $compile_time = microtime(true);

            if ($this->verbose) {
                pp([
                    "coverage_session"=>$this->sessionId,
                    "verbose"=>$this->verbose,
                ]);
                pp($coverageData);
            }
            $this->responseData = $this->apiServer->call("sendCoverage", $coverageData);
            $sent_time = microtime(true);

            if ($this->verbose) {
                var_dump("code time : " . round(($commit_time - $this->initial_time) * 1000, 2));
                var_dump("compile time : " . round(($compile_time - $commit_time) * 1000, 2));
                var_dump("send time : " . round(($sent_time - $compile_time) * 1000, 2));
                var_dump($this->responseData);
            }
        }
    }


    public function coverageIsAvailable()
    {
        return function_exists("xdebug_start_code_coverage");
    }

    /**
     * @param CoverageApiServer $apiServer
     * @return $this
     */
    public function setApiServer(CoverageApiServer $apiServer)
    {
        $this->apiServer = $apiServer;
        return $this;
    }

    /**
     * @param mixed $sessionId
     * @return CodeCoverageClient
     */
    public function setSessionId($sessionId)
    {
        if (!is_numeric($sessionId) && !empty($sessionId)) {
        } else {
            $sessionId = md5(uniqid(time()));
        }

        $this->sessionId = $sessionId;
        $this->apiServer->setSessionId($sessionId);
        return $this;
    }

    /**
     * @param string $projectName
     * @return CodeCoverageClient
     */
    public function setProjectName($projectName)
    {
        $this->apiServer->setProjectName($projectName);
        $this->projectName = $projectName;
        return $this;
    }

    /**
     * @param int $verbose
     * @return CodeCoverageClient
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * @param string $rootPath
     * @return CodeCoverageClient
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
        return $this;
    }

    public function addDataHandler(CoverageCustomData $data_handler)
    {
        $this->data_handlers[] = $data_handler;
        return $this;
    }

    /**
     * @param bool $coverage_active
     * @return CodeCoverageClient
     */
    public function setCoverageActive(bool $coverage_active): CodeCoverageClient
    {
        $this->coverage_active = $coverage_active;
        return $this;
    }

}