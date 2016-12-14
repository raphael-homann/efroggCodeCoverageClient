<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 14/12/16
 * Time: 03:17
 */

namespace Efrogg\CodeCoverage\Detector;


class ParameterDetector extends PersistedDetector
{
    protected $token;

    protected $session_parameter = "CC";
    protected $token_parameter = "cc_token";

    protected $coverage_activation_parameter = "cc_coverage";
    protected $verbose_activation_parameter = "cc_verbose";


    protected $session_name;

    /**
     * ParameterDetector constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @param string $session_parameter
     * @return ParameterDetector
     */
    public function setSessionParameter($session_parameter)
    {
        $this->session_parameter = $session_parameter;
        return $this;
    }

    /**
     * @return string
     */
    public function getSessionParameter()
    {
        return $this->session_parameter;
    }

    /**
     * @param string $coverage_activation_parameter
     * @return ParameterDetector
     */
    public function setCoverageActivationParameter($coverage_activation_parameter)
    {
        $this->coverage_activation_parameter = $coverage_activation_parameter;
        return $this;
    }

    /**
     * @return string
     */
    public function getCoverageActivationParameter()
    {
        return $this->coverage_activation_parameter;
    }

    /**
     * @param string $verbose_activation_parameter
     * @return ParameterDetector
     */
    public function setVerboseActivationParameter($verbose_activation_parameter)
    {
        $this->verbose_activation_parameter = $verbose_activation_parameter;
        return $this;
    }

    /**
     * @return string
     */
    public function getVerboseActivationParameter()
    {
        return $this->verbose_activation_parameter;
    }

    /**
     * @param mixed $token
     * @return ParameterDetector
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * @param string $token_parameter
     * @return ParameterDetector
     */
    public function setTokenParameter($token_parameter)
    {
        $this->token_parameter = $token_parameter;
        return $this;
    }

    /**
     * @return string
     */
    public function getTokenParameter()
    {
        return $this->token_parameter;
    }


    public function detect()
    {
        if ($this->detectActivation()) {
            // activation
            // si on n'en a pas un en cours
            $data=[
                "session_name"=>$_GET[$this->session_parameter]
            ];
            if(isset($_GET[$this->verbose_activation_parameter])) {
                $data["verbose"] = (int)$_GET[$this->verbose_activation_parameter];
            }
            if(isset($_GET[$this->coverage_activation_parameter])) {
                $data["coverage"] = (bool)$_GET[$this->coverage_activation_parameter];
            }
            $this->persister->persist(json_encode($data));
        } elseif ($this->detectDeactivation()) {
            {
                // désactivation
                $this->persister->delete();
            }
        }


        // détection du cookie
        if ($this->persister->exists()) {
            $data = json_decode($this->persister->read(),true);
            $this->coverage_client->setSessionId($data["session_name"]);
            if(isset($data["verbose"])) {
                $this->coverage_client->setVerbose($data["verbose"]);
            }
            if(isset($data["coverage"])) {
//                $this->coverage_client->activeCoverage($data["coverage"]);
            }
            $this->coverage_client->beginCoverage();
        }


    }

    /**
     * @return bool
     */
    public function detectActivation()
    {
        return $this->tokenIsValid() && isset($_GET[$this->session_parameter]) && (bool)$_GET[$this->session_parameter];
    }

    public function tokenIsValid()
    {
        return (isset($_GET[$this->token_parameter]) && $_GET[$this->token_parameter] == $this->token);
    }

    public function detectDeactivation()
    {
        return $this->tokenIsValid() && isset($_GET[$this->session_parameter]) && (bool)$_GET[$this->session_parameter];
    }
}