<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 29/11/16
 * Time: 10:07
 */

namespace efrogg\CodeCoverage\Persister;


class FilePersister implements PersisterInterface
{
    protected $file_name=null;

    /**
     * FilePersister constructor.
     * @param null $file_name
     */
    public function __construct($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return bool
     */
    public function persist($value)
    {
        $this->test();
        return (bool) file_put_contents($this->file_name,$value);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        $this->test();
        $value = $this->read();
        return !empty($value);
    }

    /**
     * @return string
     */
    public function read()
    {
        $this->test();
        if(file_exists($this->file_name)) {
            return file_get_contents($this->file_name);
        }
        return null;
    }

    /**
     * @param null $file_name
     * @return FilePersister
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
        return $this;
    }

    /**
     * @return null
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    private function test()
    {
        if(null == $this->file_name) {
            throw new \Exception("FilePersister : file_name cannot be null");
        }
        //TODO : path writable etc...
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if(file_exists($this->file_name)) {
            return unlink($this->file_name);
        }
        return true;
    }
}