<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 20/06/16
 * Time: 11:16
 */


namespace efrogg\CodeCoverage;

class CoverageApiServer {
    protected $server_url;
    protected $projectName = 'default';
    protected $sessionId = null;

    /**
     * CoverageApiServer constructor.
     * @param $server_url
     * ex : http://code-coverage-server/api/
     */
    public function __construct($server_url)
    {
        $this->server_url = $server_url;
    }

    public function call($method,$params) {
        $data =
        print_rr($params);
        exit;
    }

    public function setProjectName($projectName)
    {
        $this->projectName=$projectName;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId=$sessionId;
    }

}