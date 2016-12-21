<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 20/06/16
 * Time: 11:16
 */


namespace Efrogg\CodeCoverage;

use Guzzle\Http\Exception\ClientErrorResponseException;

class CoverageApiServer
{
    protected $server_url;
    protected $projectName = 'default';
    protected $sessionId = null;
    protected $token;

    protected $authUser = null;
    protected $authPass = '';
    protected $server_port = 80;
    protected $encode_utf8 = false;

    /**
     * CoverageApiServer constructor.
     * @param $server_url
     * ex : http://code-coverage-server/api/
     * @param int $port
     */
    public function __construct($server_url,$port=80)
    {
        $this->server_url = rtrim($server_url, "/");
        $this->server_port = $port;
    }

    public function call($method, $data)
    {
        $client = new \Guzzle\Http\Client();
        try {
            $request = $client->post(
                $this->server_url . "/" . $method,
                array("Content-Type" => "application/json"),
                array()
            );
            IF($this->server_port != 80) {
                $request->setPort($this->server_port);
            }

            $body = array(
                "_project" => $this->projectName,
                "_session" => $this->sessionId,
                "data" => $data
            );
            if($this->encode_utf8) {
                array_walk_recursive($body, function(&$item) { $item = utf8_encode($item); });
            }
            // LE SESSION ID EST NULL
            $request->setBody(json_encode($body));


            if (!empty($this->authUser)) {
                $request->setAuth($this->authUser, $this->authPass);
            }
            $response = $request->send();
            try {
                $data = $response->json();
            } catch(\Exception $e) {
                var_dump($e->getMessage());
                echo("<pre>".$response->getBody()."</pre>");
            }
            return $data;
            //TODO : check response
        } catch (ClientErrorResponseException $e) {
            echo "<h1>coverage : " . $e->getResponse()->getStatusCode() . " : " . $e->getResponse()->getReasonPhrase() . "</h1>";
//            var_dump($response);
//            var_dump($e);
        } catch (\Exception $e) {
            var_dump($e);
        }
    }

    /**
     * @param $user
     * @param string $pass
     * @return CoverageApiServer
     */
    public function setAuth($user, $pass = '')
    {
        $this->authUser = $user;
        $this->authPass = $pass;
        return $this;

    }

    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @param bool $encode_utf8
     * @return CoverageApiServer
     */
    public function setEncodeUtf8(bool $encode_utf8): CoverageApiServer
    {
        $this->encode_utf8 = $encode_utf8;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEncodeUtf8(): bool
    {
        return $this->encode_utf8;
    }

}