<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 20/06/16
 * Time: 11:16
 */


namespace efrogg\CodeCoverage;

use Guzzle\Http\Exception\ClientErrorResponseException;

class CoverageApiServer {
    protected $server_url;
    protected $projectName = 'default';
    protected $sessionId = null;
    protected $token;

    protected $authUser = null;
    protected $authPass = '';

    /**
     * CoverageApiServer constructor.
     * @param $server_url
     * ex : http://code-coverage-server/api/
     */
    public function __construct($server_url)
    {
        $this->server_url = rtrim($server_url,"/");
    }

    public function call($method,$data) {
        $client = new \Guzzle\Http\Client();
        try {
            $request = $client->post(
                $this->server_url . "/" . $method,
                array("Content-Type" => "application/json"),
                array()
            );
            $request->setBody(
                    json_encode(array(
                        "_project" => $this->projectName,
                        "_session" => $this->sessionId,
                        "data" => $data
                    ))
            );
            if (!empty($this->authUser)) {
                $request->setAuth($this->authUser, $this->authPass);
            }
            $response = $request->send();
//                    echo $response->getBody();
            $data = $response->json();
            return $data;
            //TODO : check response
        } catch(ClientErrorResponseException $e) {
            echo "<h1>coverage : ".$e->getResponse()->getStatusCode()." : ".$e->getResponse()->getReasonPhrase()."</h1>";
//            var_dump($response);
//            var_dump($e);
        } catch(\Exception $e) {
            var_dump($e);
        }
    }

    /**
     * @param $user
     * @param string $pass
     * @return CoverageApiServer
     */
    public function setAuth($user,$pass='')
    {
        $this->authUser = $user;
        $this->authPass = $pass;
        return $this;

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