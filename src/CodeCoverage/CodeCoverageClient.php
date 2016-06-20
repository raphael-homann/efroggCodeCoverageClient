<?php

/**
 * Created by PhpStorm.
 * User: raph
 * Date: 03/06/16
 * Time: 17:22
 */
namespace efrogg\CodeCoverage;

class CodeCoverageClient
{
    protected $projectName = 'default';
    protected $sessionId = null;
    /** @var CoverageApiServer */
    private $apiServer = null;
    private $coverageIsRunning = false;

    protected $cookieName = 'C_C';
    protected $cookieDuration = 86400;
    protected $cookiePath = "/";

    protected $getParamName = null;

    /**
     * CodeCoverageClient constructor.
     * @param $projectName
     * @param $sessionId
     * @param CoverageApiServer $apiServer
     */
    public function __construct( CoverageApiServer $apiServer)
    {
        $this->apiServer = $apiServer;
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


    public function beginCoverage() {
        if($this -> coverageIsAvailable()) {
            xdebug_start_code_coverage(XDEBUG_CC_UNUSED|XDEBUG_CC_DEAD_CODE);
            $this->coverageIsRunning = true;
        }

    }

    public function commitCoverage() {
        if($this->coverageIsRunning) {
            if(is_null($this->apiServer)) {
                throw new \Exception("Coverage Api server not configured");
            }
            $coverageData = xdebug_get_code_coverage();
            $this->apiServer -> call("sendCoverage",$coverageData);
        }
    }


    public function coverageIsAvailable() {
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
     * Le paramètre en GET permet d'activer / désactiver le cookie
     * @param null $getParamName
     * @return $this
     */
    public function setGetParamName($getParamName)
    {
        $this->getParamName = $getParamName;
        return $this;
    }

    /**
     * @param null $cookieName
     * @param int $cookieDuration
     * @param string $cookiePath
     * @return CodeCoverageClient
     */
    public function setCookieName($cookieName,$cookieDuration=86400,$cookiePath="/")
    {
        $this->cookieName = $cookieName;
        $this->cookieDuration = $cookieDuration;
        $this->cookiePath = $cookiePath;
        return $this;
    }

    /**
     *
     */
    public function handleTrigger()
    {
        if(!is_null($this->getParamName)) {
            if(isset($_GET[$this->getParamName])) {
                $activate = (bool)$_GET[$this->getParamName];

                if($activate ) {
                    // activation
                    if(!$this->cookieIsActive()) {
                        // si on n'en a pas un en cours
                        $this->activateCookie(md5(uniqid(time())));
                    }
                } else {
                    // désactivation
                    $this->deactivateCookie();
                }
            }
        }

        if($this->cookieIsActive())
        {
            $this->setSessionId($_COOKIE[$this->cookieName]);
            $this->beginCoverage();
        }
    }

    protected function activateCookie($sessionId)
    {
        if(!is_null($this->cookieName)) {
            setcookie($this->cookieName,$sessionId,time()+$this->cookieDuration,"/");
            $_COOKIE[$this->cookieName] = $sessionId;
        }
    }

    protected function deactivateCookie()
    {
        setcookie($this->cookieName,0,time()-86400,"/");
        unset($_COOKIE[$this->cookieName]);
    }

    protected function cookieIsActive() {
        return (!is_null($this->cookieName)                  // gestion par cookie active
        && isset($_COOKIE[$this->cookieName])       // cookie défini
        && (bool)$_COOKIE[$this->cookieName]);       // cookie à 1 (ou une session)
    }
    /**
     * @param mixed $sessionId
     * @return CodeCoverageClient
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @param string $projectName
     * @return CodeCoverageClient
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
        return $this;
    }

}