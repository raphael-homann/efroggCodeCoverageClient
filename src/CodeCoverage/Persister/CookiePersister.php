<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 29/11/16
 * Time: 10:02
 */

namespace Efrogg\CodeCoverage\Persister;


class CookiePersister implements PersisterInterface
{
    protected $cookie_name="CC";
    protected $cookie_duration = 86400;
    protected $cookie_path = "/";

    /**
     * CookiePersister constructor.
     * @param string $cookie_name
     * @param int $cookie_duration
     * @param string $cookie_path
     */
    public function __construct($cookie_name="CC",$cookie_duration=86400,$cookie_path="/")
    {
        $this->cookie_name = $cookie_name;
        $this->cookie_duration = $cookie_duration;
        $this->cookie_path = $cookie_path;
    }

    /**
     * @param null|string $cookie_name
     * @return CookiePersister
     */
    public function setcookie_name($cookie_name)
    {
        $this->cookie_name = $cookie_name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getcookie_name()
    {
        return $this->cookie_name;
    }

    /**
     * @param int $cookie_duration
     * @return CookiePersister
     */
    public function setCookieDuration($cookie_duration)
    {
        $this->cookie_duration = $cookie_duration;
        return $this;
    }

    /**
     * @param string $cookie_path
     * @return CookiePersister
     */
    public function setCookiePath($cookie_path)
    {
        $this->cookie_path = $cookie_path;
        return $this;
    }

    /**
     * @return int
     */
    public function getCookieDuration()
    {
        return $this->cookie_duration;
    }

    /**
     * @return string
     */
    public function getCookiePath()
    {
        return $this->cookie_path;
    }


    /*
     *
     *
     *
     */


    /**
     * @return bool
     */
    public function persist($value)
    {
        if (!is_null($this->cookie_name)) {
            $_COOKIE[$this->cookie_name] = $value;
            return setcookie($this->cookie_name, $value, time() + $this->cookie_duration, "/");
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return (!is_null($this->cookie_name)                  // gestion par cookie active
            && isset($_COOKIE[$this->cookie_name])       // cookie défini
            && (bool)$_COOKIE[$this->cookie_name]);       // cookie à 1 (ou une session)
    }

    /**
     * @return string
     */
    public function read()
    {
        return $_COOKIE[$this->cookie_name];
    }

    /**
     * @return bool
     */
    public function delete()
    {
        unset($_COOKIE[$this->cookie_name]);
        return setcookie($this->cookie_name, 0, time() - 86400, "/");
    }
}